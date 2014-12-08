// uses Google polyfill for dialog element in non-supporting browsers
// (https://github.com/GoogleChrome/dialog-polyfill)

function showImage(e) {
	e.preventDefault();
	
	//load vars
	//console.log(this.dataset.fullsize);
	fullsize = document.getElementById("masonryImg");
    fullsize.src = this.dataset.fullsize;
    
    //create cover / modal
    cover = document.getElementById("masonryCover");
    testdialog=document.createElement("dialog");
    testdialog.setAttribute("open", "");
    if (!testdialog.open){
    	dialogPolyfill.registerDialog(cover);
    }
	cover.showModal();
	
	//setup the close
	document.getElementById("masonryCoverClose").onclick = function() { 
	  fullsize.src = "";  
      cover.close(); 
    }
}

//setup on init b/c well Masonry has to get built first
function masonryLightbox(){
    var imglinks = document.getElementById("mason").getElementsByTagName("div");
    for (var i=0; i<imglinks.length; i++) { imglinks[i].onclick = showImage; }
}    


