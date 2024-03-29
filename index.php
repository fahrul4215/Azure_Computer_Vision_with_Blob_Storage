<?php
    session_start();
    if (isset($_SESSION['nama_file'])) {
        $linkBlob = "https://fahrul4215dicoding.blob.core.windows.net/".$_SESSION['nama_container']."/".$_SESSION['nama_file'];
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Submission 2 : Upload dan Analisa Gambar</title>

    <script type="text/javascript"  src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
</head>
<body>
    <?php 
        if (!isset($_SESSION['nama_file'])) {
    ?>
    <h1>Analisa Gambar:</h1>
    <br>
    Pilih gambar untuk di upload :
    <form action="phpQS.php" method="post" enctype="multipart/form-data">
        <input type="file" name="gambar" accept=".jpeg,.jpg,.png" required>
        <input type="submit" name="submit" value="upload">
    </form>
    <?php
        } else {
    ?>
    <button onclick="processImage()">Analyze image</button>
    <input type="text" name="inputImage" id="inputImage" value="<?php echo $linkBlob ?>" readonly style="width:50%">
    <br><br>
    <div id="wrapper" style="width:1020px; display:table;">
        <div id="jsonOutput" style="width:600px; display:table-cell;">
            Response:
            <br><br>
            <textarea id="responseTextArea" class="UIInput"
            style="width:580px; height:400px;"></textarea>
        </div>
        <div id="imageDiv" style="width:420px; display:table-cell;">
            Source image:
            <br><br>
            <img id="sourceImage" width="400" />
            <h3 id="captionText"></h3>
        </div>
    </div>
    <hr>
    <form method="post" action="phpQS.php?Cleanup&containerName=<?php echo $_SESSION['nama_container']; ?>">
        <button type="submit">Press to clean up all resources created by this sample</button>
    </form>
    <?php
        }
    ?>

    <script type="text/javascript">
        function processImage() {
            // **********************************************
            // *** Update or verify the following values. ***
            // **********************************************
            
            // Replace <Subscription Key> with your valid subscription key.
            var subscriptionKey = "8ff5879151014016b90d2e43375fac48";
            
            // You must use the same Azure region in your REST API method as you used to
            // get your subscription keys. For example, if you got your subscription keys
            // from the West US region, replace "westcentralus" in the URL
            // below with "westus".
            //
            // Free trial subscription keys are generated in the "westus" region.
            // If you use a free trial subscription key, you shouldn't need to change
            // this region.
            var uriBase =
            "https://southeastasia.api.cognitive.microsoft.com/vision/v2.0/analyze";
            
            // Request parameters.
            var params = {
                "visualFeatures": "Categories,Description,Color",
                "details": "",
                "language": "en",
            };
            
            // Display the image.
            var sourceImageUrl = document.getElementById("inputImage").value;
            document.querySelector("#sourceImage").src = sourceImageUrl;
            
            // Make the REST API call.
            $.ajax({
                url: uriBase + "?" + $.param(params),
                
                // Request headers.
                beforeSend: function(xhrObj){
                    xhrObj.setRequestHeader("Content-Type","application/json");
                    xhrObj.setRequestHeader(
                        "Ocp-Apim-Subscription-Key", subscriptionKey);
                },
                
                type: "POST",
                
                // Request body.
                data: '{"url": ' + '"' + sourceImageUrl + '"}',
            })
            
            .done(function(data) {
                // Show formatted JSON on webpage.
                $("#responseTextArea").val(JSON.stringify(data, null, 2));
                console.log(data);
                $("#captionText").text(data.description.captions[0].text);
            })
            
            .fail(function(jqXHR, textStatus, errorThrown) {
                // Display error message.
                var errorString = (errorThrown === "") ? "Error. " :
                errorThrown + " (" + jqXHR.status + "): ";
                errorString += (jqXHR.responseText === "") ? "" :
                jQuery.parseJSON(jqXHR.responseText).message;
                alert(errorString);
            });
        };
    </script>
</body>
</html>