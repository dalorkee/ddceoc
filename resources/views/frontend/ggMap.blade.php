@extends('layouts.template')
@section('pageStyle')
<style>
	#ds {color: red;}
</style>
@endsection
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 col-xl-6">
				<form method="get" action='{{ route('ggMap') }}' class="form-inline">
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
		<div id="ggMap" style="width: 100%; height: 100vh;"></div>
	</div>
</section>
<!-- /.content -->
@endsection
@section('pageScript')
	<script>
		var locations = [
			@php

			$dcirData->each(function($item) use ($province, $district, $subDistrict, $disease) {
				if ($item->dcir_criterion == 1) {
					$pin = 'public/images/red-dot.png';
				} else {
					$pin = 'public/images/orange-dot.png';
				}
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
				$htm = "";
				$infoView = "";
				$infoView .= "<ul class=\"list-group map-view-info\">";
				$infoView .= "<li><span>โรค</span><span>".$dsName."</span></li>";
				$infoView .= "<li><span>จำนวนผู้ป่วย</span><span>".$item->event_sickness_total."</span></li>";
				$infoView .= "<li><span>จังหวัด</span><span>".$prov[0]->province_name."</span></li>";
				$infoView .= "<li><span>อำเภอ</span><span>".$dist[0]->amphur_name."</span></li>";
				$infoView .= "<li><span>ตำบล</span><span>".$sdst[0]->tambol_name."</span></li>";
				$infoView .= "<li><span>รายละเอียด</span><span><a href=\"http://www.boeeoc.moph.go.th/eventbase/event/showevent/event_id/".$item->event_id."/\" target=\"blank\"><i class=\"fa fa-eye\"></i></a></span></li>";
				$infoView .= "</ul>";
				$htm .= "{";
				$htm .= "lat:".$item->latitude.",";
				$htm .= "lng:".$item->longitude.",";
				$htm .= "info:'".$infoView."',";
				$htm .= "pin:'".$pin."'";
				$htm .= "}, ";
				echo $htm;
			});
			@endphp
		];
		function initMap() {
			var map = new google.maps.Map(document.getElementById('ggMap'), {
				zoom: 6,
				center: new google.maps.LatLng(9.237541, 100.897475)
			});
			var infoWin = new google.maps.InfoWindow();
			var markers = locations.map(function(location, i) {
				var marker = new google.maps.Marker({
					position: location,
					icon: location['pin']
					//icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
				});
				google.maps.event.addListener(marker, 'click', function(evt) {
					infoWin.setContent(location.info);
					infoWin.open(map, marker);
				})
				return marker;
			});
			var myParser = new geoXML3.parser({map: map});
			var url = "{{ asset('gis/kml/province_border.kml') }}";
			myParser.parse(url);
			var opt = {
				styles: [{
					textColor: 'white',
					height: 53,
					url: "images/m1.png",
					width: 53
				},
				{
					textColor: 'white',
					height: 56,
					url: "images/m2.png",
					width: 56
				},
				{
					textColor: 'white',
					height: 66,
					url: "images/m3.png",
					width: 66
				},
				{
					textColor: 'white',
					height: 78,
					url: "images/m4.png",
					width: 78
				},
				{
					textColor: 'white',
					height: 90,
					url: "images/m5.png",
					width: 90
				}],
				maxZoom: 17
			}
			var markerCluster = new MarkerClusterer(map, markers, opt);
		}
	</script>
	<script>
		$(function () {
			/* Date range picker */
			$('#findEvents').daterangepicker({
				format: 'DD/MM/YYYY'
			})
		});
	</script>
	<!-- OPTIONAL SCRIPTS -->
	{{ Html::script(('AdminLTE/dist/js/demo.js')) }}
	<!-- PAGE PLUGINS -->
	{{ Html::script("vendor/geoxml3-master/kmz/geoxml3.js") }}
	{{ Html::script("vendor/googlemap/markerclusterer.js") }}

	<script async defer
	src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAU3PEohqKXN7gcmwNCGBkJNIOF75OxCBA&callback=initMap">
	</script>
	<script>
	<!-- SlimScroll 1.3.0 -->
	{{ Html::script(('AdminLTE/plugins/slimScroll/jquery.slimscroll.min.js')) }}
	<!-- Page script -->
@endsection
