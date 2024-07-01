$(document).ready(function() {
            
    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        document.getElementById('dropzone').addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    // Highlight drop area when dragging over
    ['dragenter', 'dragover'].forEach(eventName => {
        document.getElementById('dropzone').addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        document.getElementById('dropzone').addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        document.getElementById('dropzone').classList.add('border-primary');
    }

    function unhighlight(e) {
        document.getElementById('dropzone').classList.remove('border-primary');
    }

    // Handle dropped files
    document.getElementById('dropzone').addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
       
        let dt = e.dataTransfer;
        let files = dt.files;

        //handleFiles(files);
               
        for (let i = 0; i < files.length; i++) {
            let file = files[i];

            // Check if it's an Excel file (optional, you can adjust the MIME type check as needed)
             if (/*file.type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||  // XLSX
                file.type === 'application/vnd.ms-excel' ||  */                                   // XLS
                file.type === 'text/csv') {     
                                                          
                $('#alert').show('fade');
                displayFileContent(file);
            } else {
                alert('Please upload an CSV file.');
            }
        }
    }

   /* function handleFiles(files) {

    }*/
        function displayFileContent(file) {
        let reader = new FileReader();

        reader.onload = function(e) {
            let content = e.target.result;
           $('#save').show();
            document.getElementById('file-content').innerHTML = '<textarea style="display:none" name="content">' + content + '</textarea><pre></pre>';
        };

        reader.readAsText(file);
    }
   /* $('#uploadForm').submit(function(e) {
       
    });*/
 });