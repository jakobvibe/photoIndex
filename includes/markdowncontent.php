<?php
	$markdownData = array();
	if($markdownName && file_exists($markdownName)) {
		$ParsedownExtra = new ParsedownExtra();
		$markdownFile = file_get_contents($markdownName);
		if ($markdownFile !== false) {
			$markdownText = $ParsedownExtra->text($markdownFile);
			
			$DOM = new DOMDocument;
			$DOM->loadHTML('<meta charset="utf-8">'.$markdownText);

			//get metadata tag
			$items = $DOM->getElementsByTagName('metadata');
			
			if ($items->length > 0) {
				$node = $items->item(0); 
				$jsonArray = json_decode($node->nodeValue, true);
				$markdownData['metadata'] = $jsonArray;
				$node->parentNode->removeChild($node);
			}
			
			$markdownData['content'] = $DOM->saveHTML();
		}
	}