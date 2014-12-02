MODX-Masonry
============

Simple &amp; Advanced Tutorials to integrate Masonry with MODX

Cloud URL = http://masonry.clients.modxcloud.com/

##Goals

 1. Basic Starter DIY Tutorials
 2. Auto-watch images in a folder
 3. Mobile Upload
 4. More Themes

##Installation

 1. Upload - `assets/js/masonry.pkgd.min.js`
 2. Upload - `assets/css/demo.css`
 3. Create - `assets/img` (naming convention optional)
 4. Create Chunk - `MasonryConfig` (copy from the repo)
 5. Add to `<head>` - 
    ```
    [[$MasonryConfig? &width=`200` &theme=`demo`]]
    ```
 6. Add Container HTML
    ```
    <div class="container" id="mason">
      <div class="item" style="background-image: url([[phpthumbof? &input=`folder/file.jpg` &options=`&w=400&zc=0&aoe=0&far=0`]]);"></div>
    </div> <!-- /#mason -->
    ```
 7. the **phpthumbof** width should be double the width in the chunk   
