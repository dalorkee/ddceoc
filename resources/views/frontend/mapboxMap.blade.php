@extends('layouts.template')
@section('pageStyle')
	{{ Html::style('https://api.tiles.mapbox.com/mapbox-gl-js/v0.49.0/mapbox-gl.css') }}
	<style>
		.mapboxgl-popup {
			max-width: 400px;
			font: 12px/20px 'Helvetica Neue', Arial, Helvetica, sans-serif;
		}
		ul.evb-popup {
			min-width: 200px;
			margin: 0;
			padding: 0;
			list-style: none;
		}
		ul.evb-popup li {
			margin: 3px 0;
			padding: 0 0 0 5px;
		}
		ul.evb-popup li>span:first-child {
			margin-right: 2px;
			font-weight: bold;
			display: inline-block;
			width: 90px;
		}
	</style>
@endsection
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 col-xl-6">
				<form method="get" action='{{ route('mBox') }}' class="form-inline">
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">
									<i class="far fa-calendar-alt"></i>
								</span>
							</div>
							<input type="text" name="date_range" class="form-control float-right" id="findEvents" placeholder="เลือกช่วงเวลา">
							<button type="submit" class="btn border border-left-0 rounded-right rounded-bottom" style="margin-left:-2px;">
								<i class="fas fa-search" style="color:#495057;"></i>
							</button>
						</div>
					</div>
				</form>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 col-xl-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Home</a></li>
					<li class="breadcrumb-item active">Events</li>
				</ol>
			</div>
		</div>
	</div>
</div>
<!-- /.content-header -->
<!-- Main content -->
<section class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
				<h4 class="event-date-h4">Events Notifications {{ $eventAgg['dRange'] }}</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 col-xl-3">
				<div class="info-box mb-3">
					<span class="info-box-icon bg-info elevation-1"><i class="fas fa-info-circle"></i></span>
					<div class="info-box-content">
						<span class="info-box-text">Total events</span>
						<span class="info-box-number">{{ number_format($eventAgg['totalCase']) }}</span>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 col-xl-3">
				<div class="info-box mb-3">
					<span class="info-box-icon bg-danger elevation-1"><i class="fas fa-ambulance"></i></span>
					<div class="info-box-content">
						<span class="info-box-text">Ongoing events, DCIR</span>
						<span class="info-box-number">{{ number_format($eventAgg['dcirCase']) }}</span>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 col-xl-3">
				<div class="info-box mb-3">
					<span class="info-box-icon bg-warning elevation-1"><i class="far fa-bell"></i></span>
					<div class="info-box-content">
						<span class="info-box-text">Ongoing events, Non DCIR</span>
						<span class="info-box-number">{{ number_format($eventAgg['nonDcirCase']) }}</span>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 col-xl-3">
				<div class="info-box mb-3">
					<span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
		 			<div class="info-box-content">
						<span class="info-box-text">Finished events</span>
						<span class="info-box-number">{{ number_format($eventAgg['dcirFinished']) }}</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="container-fluid">
		<div id="map" style="width: 100%; height: 100vh;"></div>
	</div>
</section>
<!-- /.content -->
@endsection
@section('pageScript')
	<!-- OPTIONAL SCRIPTS -->
	{{ Html::script(('public/AdminLTE/dist/js/demo.js')) }}
	<!-- PAGE PLUGINS -->
	{{ Html::script("https://api.tiles.mapbox.com/mapbox-gl-js/v0.49.0/mapbox-gl.js") }}
	<!-- SlimScroll 1.3.0 -->
	{{ Html::script(('public/AdminLTE/plugins/slimScroll/jquery.slimscroll.min.js')) }}

	<script>
		$(function () {
			/* Date range picker */
			$('#findEvents').daterangepicker({
				format: 'DD/MM/YYYY'
			})
		});
	</script>
	<script>
		mapboxgl.accessToken = 'pk.eyJ1IjoiZGFsb3JrZWUiLCJhIjoiY2pnbmJrajh4MDZ6aTM0cXZkNDQ0MzI5cCJ9.C2REqhILLm2HKIQSn9Wc0A';
		var map = new mapboxgl.Map({
			container: 'map',
			style: 'mapbox://styles/mapbox/dark-v9',
			center: [ 100.897475, 9.237541],
			zoom: 4.6
		});

		map.on('load', function() {
			map.addSource("evb", {
				type: "geojson",
				data:
				{
					"crs": {
						"properties": {
							"name": "urn:ogc:def:crs:OGC:1.3:CRS84"
						},
						"type": "name"
					},
					"features": [
					@php
					$dcirData->each(function($item) use ($province, $district, $subDistrict, $disease) {
						$prov = $province->where('province_id', $item->province_id);
						$prov = $prov->values();
						$dist = $district->where('amphur_id', $item->amphur_id);
						$dist = $dist->values();
						$sdst = $subDistrict->where('tambol_id', $item->tambol_id);
						$sdst = $sdst->values();
						if ($item->disease_id == "อื่นๆ ระบุ") {
							$dsName = "อื่นๆ";
						} else {
							$dsName = $disease[$item->disease_id]->disease_name;
						}
							$popup = "<ul class='evb-popup'>";
							$popup .= "<li><span>โรค</span><span>".$dsName."</span></li>";
							$popup .= "<li><span>จำนวนผู้ป่วย</span><span>".$item->event_sickness_total."</span></li>";
							$popup .= "<li><span>จังหวัด</span><span>".$prov[0]->province_name."</span></li>";
							$popup .= "<li><span>อำเภอ</span><span>".$dist[0]->amphur_name."</span></li>";
							$popup .= "<li><span>ตำบล</span><span>".$sdst[0]->tambol_name."</span></li>";
							$popup .= "<li><span>รายละเอียด</span><span><a href='http://www.boeeoc.moph.go.th/eventbase/event/showevent/event_id/".$item->event_id."/' target='blank'><i class='fa fa-eye'></i></a></span></li>";
							$popup .= "</ul>";
							$str =
							 "{
								\"geometry\": {
									\"coordinates\": [".($item->longitude).",".($item->latitude)."],
									\"type\": \"Point\"
								},
								\"properties\": {
									\"description\": \"".$popup."\",
									\"desc\": \"".$popup."\",
									\"id\": \"ev".$item->event_id."\",
									\"dcir\": ".$item->dcir_criterion."
								},
								\"type\": \"Feature\"
							},";
							echo $str;
						});
					@endphp
					],
					"type": "FeatureCollection"
				},
				cluster: true,
				clusterMaxZoom: 14,
				clusterRadius: 50
			});

			map.addLayer({
				id: "clusters",
				type: "circle",
				source: "evb",
				filter: ["has", "point_count"],
				paint: {
					"circle-color": [
						"step",
						["get", "point_count"],
						"#51bbd6",
						100,
						"#DE4150",
						300,
						"#f28cb1",
						600,
						"#ff00ff"
					],
					"circle-radius": [
						"step",
						["get", "point_count"],
						20,
						100,
						30,
						750,
						40
					]
				}
			});

			map.addLayer({
				id: "cluster-count",
				type: "symbol",
				source: "evb",
				filter: ["has", "point_count"],
				layout: {
					"text-field": "{point_count_abbreviated}",
					"text-font": ["DIN Offc Pro Medium", "Arial Unicode MS Bold"],
					"text-size": 12
				}
			});

			map.addLayer({
				id: "unclustered-point",
				type: "circle",
				source: "evb",
				filter: ["!", ["has", "point_count"]],
				paint: {
					'circle-color': [
						'match',
						['get', 'dcir'],
						1, '#e55e5e',
						2, '#28A745',
						'#cccccc'
					],
					"circle-radius": 4,
					"circle-stroke-width": 1,
					"circle-stroke-color": "#fff"
				}
			});

			// inspect a cluster on click
			map.on('click', 'clusters', function (e) {
				var features = map.queryRenderedFeatures(e.point, { layers: ['clusters'] });
				var clusterId = features[0].properties.cluster_id;
				map.getSource('evb').getClusterExpansionZoom(clusterId, function (err, zoom) {
					if (err)
						return;
					map.easeTo({
						center: features[0].geometry.coordinates,
						zoom: zoom
					});
				});
			});

			map.on('mouseenter', 'clusters', function () {
				map.getCanvas().style.cursor = 'pointer';
			});

			map.on('mouseleave', 'clusters', function () {
				map.getCanvas().style.cursor = '';
			});

			map.on('click', 'unclustered-point', function (e) {
				var coordinates = e.features[0].geometry.coordinates.slice();
				var desc = e.features[0].properties.desc;

				while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
					coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
				}

				new mapboxgl.Popup()
				.setLngLat(coordinates)
				.setHTML(desc)
				.addTo(map);
			});


			// Change the cursor to a pointer when the mouse is over the places layer.
			map.on('mouseenter', 'unclustered-point', function () {
				map.getCanvas().style.cursor = 'pointer';
			});

			// Change it back to a pointer when it leaves.
			map.on('mouseleave', 'unclustered-point', function () {
				map.getCanvas().style.cursor = '';
			});
		});
	</script>
@endsection
