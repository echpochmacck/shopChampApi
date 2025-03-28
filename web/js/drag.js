$(() => {
    let files = [];



    $('#box').on('dragenter', function (e) {
        e.preventDefault();
    });

    $('#box').on('dragleave', function (e) {
        e.preventDefault();
    });

    $('#box').on('dragover', function (e) {
        e.preventDefault();
        e.originalEvent.dataTransfer.files = '';
    });

    $('#box').on('drop', function (e) {
        e.preventDefault();
        const droppedFiles = e.originalEvent.dataTransfer.files;

        if (files.length) {
            files = files.concat([...droppedFiles]);
        } else {
            files = [...droppedFiles];
        }

        const dataTransfer = new DataTransfer();
        files.forEach(file => dataTransfer.items.add(file));
        // console.log(dataTransfer)
        // console.log($('#hiddenInpt'))
        $('#hiddenInpt')[0].files = dataTransfer.files
        // console.log($('#hiddenInpt')[0].files)
        printFiles(files);
    });

    function printFiles(array) {
        $('#list').html('');
        // console.log(array);
        array.forEach(function (val) {
            $('#list').append(`<li>${val.name}</li>`);
        });
    }

});