<?php

	class ImageController extends Zend_Controller_Action {
		private $request;
		private $imageTable;
		private $imageSizeTable;
		private $supportedTypes = array(
			array(
				'createfrom' => 'imagecreatefromjpeg',
				'extension' => array('jpg', 'jpeg'),
				'render' => 'imagejpeg'
			),
			array(
				'createfrom' => 'imagecreatefrompng',
				'extension' => 'png',
				'render' => 'imagepng'
			)				
		);
		
		public function init() {
			// Rendern abschalten
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true); //YOLO
			$this->request = $this->getRequest();
			
			$this->imageTable = new Application_Model_DbTable_ImageTable();
			$this->imageSizeTable = new Application_Model_DbTable_ImageSizeTable();
		}

		public function indexAction() {
			$imagePath = null;
			$imageID = null;
			
			if($this->request->isGet()) {
				$imageID = $this->request->getParam('imageid');
				
				if($imageID != null) {
					$imagePath = $this->imageTable->getImagePathByID($imageID);
				}
				
				// Prüfen ob Bild existiert, ansonsten nehme Default Image
				if($imageID == null || !file_exists($imagePath)) {
					$imagePath = APPLICATION_PATH.'/resources/images/noimage.png';
				}
				
				$imageExtension = $this->getFileExtension($imagePath);
				$mode = 'proportional';
				
				if(($imageMode = $this->request->getParam('imagemode')) != null) {
					if($imageMode == 'crop' || $imageMode == 'proportional') {
						$mode = $imageMode;
					}
				}
				
				
				
				$imageArr = array(
					'mode' => $mode,
					'dimension' => null,
					'execute' => null,
					'extension' => $imageExtension,
					'filepath' => $imagePath,
					'info' => $this->getImageInfo($imagePath) ,
					'sizename' => 'full'
				);
				
				if(($getImageType = $this->request->getParam('imagetype')) != null) {
					/**
					 * vordefinierte groessen aus der DB auslesen
					 * und gegenpruefen
					 */
					$imageTypes = $this->getImageSizes();
					
					foreach($imageTypes as $type) {
						if($type['typ'] == $getImageType) {
							$imageArr['dimension'] = array($type['width'], $type['height']);
							$imageArr['sizename'] = $type['typ'];
							break;
						}
					}
				}
				
				
				/** 
				 * Unterstuetzte Typen
				 */
				if(($exeArr = $this->getExecutionDataByExtension($imageArr['extension'])) == null) {
					return null;
				}
				
				$imageArr['execute'] = $exeArr;
				
				
				
				
				if(($image = $this->getCacheFile($imageArr)) == null) {
				
					if($imageArr['dimension'] == null) {
						$image = $this->getImageResource($imageArr);
					} else {
						$image = $this->resize($imageArr);
					}
				}
				
				if($image != null) {
					$this->cacheImage($image, $imageArr);
					$this->sendImage($image, $imageArr);
					imagedestroy($image);
				}
			}
		}
		
		private function getCacheFile($imageArr) {
			$cache_file = $this->getCacheFilePath($imageArr);

			$image = null;
			
			if(file_exists($cache_file)) {
				$imageArr['filepath'] = $cache_file;
				$image = $this->getImageResource($imageArr);
			}
			
			return $image;
		}
		
		private function getCacheFilePath($imageArr) {
			$d = $this->getPathWithoutExtension($imageArr['filepath']).'-'.$imageArr['sizename'].'_'.$imageArr['mode'].'.'.$imageArr['extension'];
			
			return $d;
		}
		
		private function cacheImage($image, $imageArr) {
			$imageArr['execute']['render']($image, $this->getCacheFilePath($imageArr));	
		}
		
		private function getPathWithoutExtension($path) {
			$strl = strrpos($path, '.');
			return substr($path, 0, $strl);
		}
		
		private function getImageSizes() {
			$types = $this->imageSizeTable->getAllTypes();
			
			return $types;
		}
		
		private function sendImage($image, $arr) {
			header('Content-Type: '.$arr['info']['mime']);
			$arr['execute']['render']($image);		
		}
		
		private function getExecutionDataByExtension($ext = null) {
			$data = null;
			if($ext != null) {
				foreach($this->supportedTypes as $stArr) {
					if(is_array($stArr['extension'])) {
						foreach($stArr['extension'] as $stExt) {
							if($stExt == $ext) {
								$data = $stArr;
								break;
							}
						}
					} elseif($ext == $stArr['extension']) {
						$data = $stArr;
					}
					
					if($data != null) {
						break;
					}
				}
			}

			return $data;
		}
		
		private function getImageResource($imageArr) {
			return $imageArr['execute']['createfrom']($imageArr['filepath']);
		}
		
		
		/**
		 * mode, dimension, filepath, extension
		 * mode => crop, proportional
		 */
		private function resize($imageArr) {
			$imageSize = $imageArr['info'];
			$resizedImg = null;
			$original_image = $this->getImageResource($imageArr);
			$new_pos_W = 0;
			$new_pos_H = 0;
			
			/**
			 * Proportionale Berechnung 
			 */
			if(($imageSize[0] >= $imageArr['dimension'][0] || $imageSize[1] >= $imageArr['dimension'][1])) {
				if($imageSize[0] > $imageSize[1] || ($imageArr['dimension'][1] < $imageSize[1] && $imageArr['mode'] != 'crop')) {
					$multiplier = $imageSize[0] / $imageSize[1];
					$new_width = $imageArr['dimension'][1] * $multiplier;
					$new_height = $imageArr['dimension'][1];
					
					if($imageArr['dimension'][1] > $imageSize[1] && $imageArr['mode'] != 'crop') {
						$new_width = $imageSize[1];
						$new_height = $imageSize[1] * $multiplier;
					}
				} else {
					$multiplier = $imageSize[1] / $imageSize[0];
					$new_width = $imageArr['dimension'][0];
					$new_height = $imageArr['dimension'][0] * $multiplier;
					
					if($imageArr['dimension'][0] > $imageSize[0] && $imageArr['mode'] != 'crop') {
						$new_width = $imageSize[0];
						$new_height = $imageSize[0] * $multiplier;
					}
				}
				
				$new_width = (int) $new_width;
				$new_height = (int) $new_height;
				
				
				$render_width = $new_width;
				$render_height = $new_height;
				
				/*
				Bild Rendern		
				*/
				$resizedImg = imagecreatetruecolor($render_width, $render_height);
				
				imagecopyresampled(
					$resizedImg,
					$original_image,
					0,0,
					0,0,
					$render_width,
					$render_height,
					$imageSize[0],
					$imageSize[1]
				);
				
				imagedestroy($original_image);
			} else { // nichts zu resizen
				$resizedImg = $original_image;
			}
			
			
			/** 
			 * Crop berechnen
			 */
			if($imageArr['mode'] == 'crop' 
				&& ($imageSize[0] >= $imageArr['dimension'][0] || $imageSize[1] >=$imageArr['dimension'][1])) {
				
				$new_pos_W = 0 - (($new_width - $imageArr['dimension'][0])/2);
				$new_pos_H = 0 - (($new_height - $imageArr['dimension'][1])/2);
				
				
				$crop_image = imagecreatetruecolor($imageArr['dimension'][0], $imageArr['dimension'][1]);
				imagecopy(
					$crop_image,
					$resizedImg,
					$new_pos_W, $new_pos_H,0,0,
					$render_width,
					$render_height				
				);				
				
				imagedestroy($resizedImg);
				$resizedImg = $crop_image;
			} 
			
			
			
			return $resizedImg;
		}
		
		private function getImageInfo($path) {
			return getimagesize($path);
		}
		
		private function getFileExtension($name) {
			$ext = null;
			if(!empty($name)) {
				$ext = substr($name, strrpos($name, '.') + 1);
			}
			
			return strtolower($ext);
		}
	}

