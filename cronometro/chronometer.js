var startTime = 0
        var start = 0
        var end = 0
        var diff = 0
        var timerID = 0

        function chrono() {
            end = new Date()
            diff = end - start
            diff = new Date(diff)
            var msec = diff.getMilliseconds()
            var sec = diff.getSeconds()
            var min = diff.getMinutes()
            var hr = diff.getHours() - 1
            if (min < 10) {
                min = "0" + min
            }
            if (sec < 10) {
                sec = "0" + sec
            }
            if (msec < 10) {
                msec = "00" + msec
            } else if (msec < 100) {
                msec = "0" + msec
            }
            $('#chronotime').html(hr + ":" + min + ":" + sec + ":" + msec)
            timerID = setTimeout("chrono()", 10)
        }


        function chronoStop() {
            $('#startstop').val("Resume")
            $('#startstop').attr("onclick", "chronoContinue()")
            $('#reset').attr("onclick", "chronoStopReset()")
            clearTimeout(timerID)
        }

        // INICIAR CRONOMETRO
        function chronoStart() {
            $('#startstop').val('Stop')
            $('#startstop').attr("onclick", "chronoStop()")
            $('#reset').attr("onclick", "chronoReset")
            start = new Date()
            chrono()
        }

        // CONTNUAR TIEMPO
        function chronoContinue() {
            $('#startstop').val("Stop")
            $('#startstop').attr("onclick", "chronoStop()")
            $('#reset').attr("onclick", "chronoReset()")
            start = new Date() - diff
            start = new Date(start)
            chrono()
        }

        // RESETEAR CRONOMETRO
        function chronoReset() {
            $('#chronotime').html("0:00:00:000")
            start = new Date()
        }


        function chronoStopReset() {
            $('#chronotime').html("0:00:00:000")
            $('#startstop').attr("onclick", "chronoStart()")
        }