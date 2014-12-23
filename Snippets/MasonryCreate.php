<?php
/*
 * getImages / Masonry Extra
 * http://modx.com/extras/package/getimages
 * DESCRIPTION
 *
 * This Snippet retrieves image files from a directory, processes them through MASONRY 
 * template chunks and returns the resulting divs.
 *
 * PARAMETERS:
 * getImages:
 *   version 2.3.0
 *   author Jerry Mercer (ultravision.net)
 *
 * Masonry MODX Extra
 *   version 1.0
 *   Wayne Roddy (@dubrod)
 *
 * USAGE:
 *
 * [[MasonryCreate? 
 *   &getImages_Folder=`assets/img` 
 *   &getImages_Ext =`jpg,jpeg`
 *   &lrgImgOffset = `2`
 *   &getImages_Page_Tpl =`Masonry_Page_Tpl`
 *   &getImages_Image_Tpl =`Masonry_Image_Tpl`
 *   &thumbWidth = `200`
 *   &largeWidth = `900`
 *   &lightbox = `1`
 * ]]
 *
 *  lrgImgOffset = the # iteration for the larger image class
 *
 */

/***** SET VARIABLES *****/
//$folder = isset($_GET['folder']) ? $_GET['folder'] : ''; // use to get folder from URL
$folder = $modx->getOption('getImages_Folder', $scriptProperties, 'assets/photos'); // use to get default folder, or from parameters
$ext = $modx->getOption('getImages_Ext', $scriptProperties, 'jpg'); // What extension(s) do we use
$fileSort = $modx->getOption('getImages_Sort', $scriptProperties, 'filemtime'); // What to sort the images by
$image_tpl = $modx->getOption('getImages_Image_Tpl', $scriptProperties, 'getImages_Image_Tpl'); // template for each image
$page_tpl = $modx->getOption('getImages_Page_Tpl', $scriptProperties, 'Masonry_Page_Tpl'); // template for completed page 
$width = $modx->getOption('getImages_Width', $scriptProperties, 300); // Width to use
$height = $modx->getOption('getImages_Height', $scriptProperties, 225);// Height to use
$border = $modx->getOption('getImages_Border', $scriptProperties, 0); // Border to use
$class = $modx->getOption('getImages_Class', $scriptProperties, ''); // class name
$pageClass = $modx->getOption('getImages_PageClass', $scriptProperties, ''); // class name
$infoClass = $modx->getOption('getImages_InfoClass', $scriptProperties, ''); // class name
$exifClass = $modx->getOption('getImages_ExifClass', $scriptProperties, ''); // class name
$id = $modx->getOption('getImages_Id', $scriptProperties, ''); // id name
$pageId = $modx->getOption('getImages_PageId', $scriptProperties, ''); // id name
$infoId = $modx->getOption('getImages_InfoId', $scriptProperties, ''); // id name
$exifId = $modx->getOption('getImages_ExifId', $scriptProperties, ''); // id name
$paging = $modx->getOption('getImages_Paging', $scriptProperties, 1); // Do we use paging

$fPath = ''; // initialize full path to image variable
$imgFile = ''; // initialize full file name of image variable
$imgName = ''; // initialize image name variable
$path = ''; // initialize fPath less file name variable
$pPath = ''; // initialize path to parent folder variable
$parent = ''; // initialize parent folder variable
$fHTML = ''; // initialize formatted HTML from template chunk variable
$c = 1; // initialize counter for foreach loop variable

//masonry vars
$lrgImgOffset = $modx->getOption('lrgImgOffset', $scriptProperties, '3');
$thumbWidth = $modx->getOption('thumbWidth', $scriptProperties, '200');
$largeWidth = $modx->getOption('largeWidth', $scriptProperties, '800');
$lightbox = $modx->getOption('lightbox', $scriptProperties, '0');

/***** CREATE OPENING CONTAINER *****/

/***** CREATE IMAGE ARRAY *****/
$allImages = glob($folder.'/*.{'.$ext.'}', GLOB_BRACE); // get images($images) from image folder($folder)
array_multisort(array_map($fileSort, $allImages), SORT_DESC, $allImages); // sort the array 
$tot = count($allImages);	        // counter for all images in folder
//$modx->log(modX::LOG_LEVEL_ERROR,'total: ' . $tot);

if ($paging > 0) {
	$images = array_slice($allImages, $offset, $limit);// Type your code here
}
else {
	$images = $allImages;
}

/***** CREATE THE IMAGE SECTION FOR OUR PAGE *****/
foreach ($images as $image) // loop through the images array
{
	$modx->setPlaceholder('id', $id); // set id place-holder for use in template chunk
	$modx->setPlaceholder('count', $c); // set count place-holder
	$fPath = $image; // set full path to image
	$imgFile = basename($fPath); // strip to just image file name
	$imgName = rtrim($imgFile, ".".substr(strrchr($imgFile, "."), 1)); // strip extension
	$path = preg_replace('/'. preg_quote($imgFile, '/') . '$/', '', $fPath); // get path without file name	
	$fold = basename($path); // strip to get image folder
	$pPath = preg_replace('/'. preg_quote($fold, '/') . '\/$/', '', $path); // get path to parent folder 
	$parent = basename($pPath); // strip to get just parent folder
	$exif_data = exif_read_data ($fPath ,'IFD0' ,0 ); // read the exif data from the image
	$modx->setPlaceholder('imgLink', $fPath); // set imgLink place-holder
	$modx->setPlaceholder('imgName', $imgName); // set imgName place-holder
	$modx->setPlaceholder('path', $path); // set path place-holder
	$modx->setPlaceholder('pPath', $pPath); // set parent path place-holder
	$modx->setPlaceholder('parent', $parent); // set parent place-holder
	$modx->setPlaceholder('imgFile', $imgFile); // set imgFile place-holder
	$modx->setPlaceholder('folder', $fold); // set folder place-holder
	$modx->setPlaceholder('imgCamera', $exif_data['Model']); // set imgCamera place-holder
	$modx->setPlaceholder('imgDate', $exif_data['DateTime']); // set imgDate place-holder
	//Masonry vars
	$modx->setPlaceholder('thumbWidth', $thumbWidth);
	$modx->setPlaceholder('largeWidth', $largeWidth);
	
	//set the large image offset
	if (($c % $lrgImgOffset) == 0){
      $modx->setPlaceholder('class', "masonry-item w2");
    } else {
      $modx->setPlaceholder('class', "masonry-item");
    }
	
	//final chunk output
	$fHTML = $modx->getChunk($image_tpl); //call the formatting chunk
	//$modx->log(modX::LOG_LEVEL_ERROR,'image tpl: ' . $fHTML);
	$photos .= $fHTML; // insert completed image sections into a string
	$c = $c+1; // increment the counter	
}

if($lightbox){
    //append $photos with lightbox modal
	$modal = $modx->getChunk("Masonry_Modal_Tpl"); 
	$photos .= $modal;
}

//send photos to Masonry_Page_Tpl
$modx->setPlaceholder('photos', $photos);
//set Page Chunk
$page = $modx->getChunk($page_tpl);

return $page;
