function camera_scanning_start(target) {
	Quagga.init({
        inputStream : {
            type : 'LiveStream',
            target: target,
            constraints: {
                width: 480,
                height: 360,
                facingMode: 'environment'
            }
        },
        numOfWorkers: 2,
        frequency: 10,
        locator: {
            patchSize: 'large',
            halfSample: true
        },
        decoder : {
            readers : ['code_128_reader']
        },
        locate: true
    }, function(err) {
        if (err) {
            console.log(err);
            return;
        }

        Quagga.start();
    });
}

function camera_scanning_stop() {
	Quagga.stop();

    last_scanned_result = '';
}

last_scanned_result = '';

Quagga.onProcessed(function(result) {
    var drawingCtx = Quagga.canvas.ctx.overlay,
        drawingCanvas = Quagga.canvas.dom.overlay;

    if (result) {
        if (result.boxes) {
            drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute('width')), parseInt(drawingCanvas.getAttribute('height')));
            result.boxes.filter(function (box) {
                return box !== result.box;
            }).forEach(function (box) {
                Quagga.ImageDebug.drawPath(box, {x: 0, y: 1}, drawingCtx, {color: '#64a0d2', lineWidth: 2});
            });
        }

        if (result.box) {
            Quagga.ImageDebug.drawPath(result.box, {x: 0, y: 1}, drawingCtx, {color: '#28D094', lineWidth: 2});
        }

        if (result.codeResult && result.codeResult.code) {
            Quagga.ImageDebug.drawPath(result.line, {x: 'x', y: 'y'}, drawingCtx, {color: '#28D094', lineWidth: 3});
        }
    }
});

Quagga.onDetected(function(result) {
    var tracking_number = result.codeResult.code;

    if (tracking_number.length >= 12) {
        if (last_scanned_result !== tracking_number) {
            last_scanned_result = tracking_number;

            camera_scan_detected(tracking_number);
        }
    }
});

