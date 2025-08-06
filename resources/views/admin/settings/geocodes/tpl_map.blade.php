<!DOCTYPE html>
<html>

<head>
    <title>TPLMaps Simple Map</title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="https://fonts.googleapis.com/css?family=Raleway&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://api.tplmaps.com/js-api-v2/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="sample-map.css">
    <script src="https://api1.tplmaps.com/js-api-v2/assets/tplmaps.js?api_key=$2a$10$ixuhTqrlyD8pJfDY8FjO9OovMcIrBXIp2sUSHaJqeIjcNrpCyvHJ2"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://api.tplmaps.com/js-api-v2/assets/js/bootstrap.min.js"></script>
    <script src="sample-map.js"></script>

    <style>
        body {
            font-family: 'Raleway', sans-serif !important;
        }

        .map {
            width: 100%;
            height: 100vh;
        }

        .nav-tabs .nav-item.show .nav-link,
        .nav-tabs .nav-link.active,
        .nav-tabs .nav-item.show .nav-link,
        .nav-tabs .nav-link.active:hover,
        .nav-tabs .nav-item.show .nav-link,
        .nav-tabs .nav-link.active:focus {
            color: #ffffff;
            background-color: rgba(39, 40, 34, 1);
            border-color: rgba(39, 40, 34, 1);
            border-bottom-color: transparent;
        }

        .panel-primary>.panel-heading {
            background-color: #ffffff !important;
            border: none;
        }

        .nav-tabs {
            border: none;
        }

        pre[class*="language-"] {
            margin: 0 !important;
        }

        ul.nav-tabs {
            margin-bottom: 0px;
            border-bottom: 0px;
            overflow: hidden;
        }

        .nav-tabs>li>a,
        .nav-tabs>li>a:hover {
            border-color: #e5e6eb;
            background-color: #e5e6eb;
            color: #686868;
            border-top-right-radius: 4px !important;
            border-top-left-radius: 4px !important;
            font-family: Raleway;
            font-style: normal;
            font-weight: bold;
            font-size: 12px;
        }

        .space-aside {
            margin: 0px 3px;
        }

        #ph-code.panel-heading {
            padding-bottom: 0px;
        }

        #pb-code.panel-body {
            padding-top: 0px;
        }

        code {
            border: none;
        }

        form {
            height: 45vh;
        }

        pre {
            margin: 0px;
            border: 0px;
            width: 100%;
            height: 80vh;
        }

        div.code-toolbar>.toolbar button {
            background-color: #ffffff;
            border: none;
            color: #000000;
            padding: 5px 15px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 12px;
        }

        /* css for scroll */
        /* width */

        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        /* Track */

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        /* Handle */

        ::-webkit-scrollbar-thumb {
            background: #888;
        }

        /* Handle on hover */

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>

<body>
<div class="container-fluid">
    <div class="row">
        <div id="map" class="map"></div>
    </div>
</div>

<script src="https://maps-sdk.trimblemaps.com/addon/trimblemaps-draw-2.2.0.js"></script>

<link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
/>
<script src="https://unpkg.com/[email protected]/dist/leaflet.js"></script>
<link
        rel="stylesheet"
        href="https://unpkg.com/[email protected]/dist/leaflet.css"
/>

<div id="map" style="width:100%; height:500px;"></div>
<script>
    window.onload = function() {
        // Coordinates
        // const coords = [
        //     [24.94702561600005, 67.14470725800003],
        //     [24.952401399814104, 67.14107063685101]
        //     //     [25.01016113189251,67.03803668163549]
        // ];

        const coords = @json($coords);
        console.log(coords);
        if (!coords.length) {
            alert('No coordinates found');
            return;
        }

        // Initialize map
        const map = TPLMaps.map.initMap({
            lat: coords[0].lat,
            lng: coords[0].lng,
            zoom: 13,
            divID: "map",
            gestureHandling: true
        });

        // Create Font Awesome divIcon
        const faIcon = L.divIcon({
            html: '<i class="fa fa-map-marker fa-3x" style="color: blue;"></i>',
            iconSize: [36, 36],
            iconAnchor: [18, 36],
            className: '' // clear default CSS
        });

        // Add both markers
        // coords.forEach(([lat, lng]) => {
        //     L.marker([lat, lng], { icon: faIcon }).addTo(map);
        // });
        // Add markers
        coords.forEach(({ lat, lng }) => {
            L.marker([lat, lng], { icon: faIcon }).addTo(map);
        });
    };
</script>


</body>

</html>
