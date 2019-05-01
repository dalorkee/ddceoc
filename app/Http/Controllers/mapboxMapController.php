<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class mapBoxMapController extends evbController
{
	public function index(Request $request) {
		if (isset($request) && isset($request->date_range)) {
			$dateRange = $request->date_range;
		} else {
			$dateRange = $this->setNowDateRange();
		}
		$eventAgg = $this->eventsAggregate($dateRange);
		$dcirData = parent::dcirData($dateRange);
		//dd($dcirData);
		$province = $this->getProvince($dcirData);
		$district = $this->getDistrict($dcirData);
		$subDistrict = $this->getSubDistrict($dcirData);
		$diseaseList = parent::disease();
		$disease = $this->getDisease($diseaseList);
		return view(
			'frontend.mapboxMap',
			[
				'eventAgg'=>$eventAgg,
				'dcirData'=>$dcirData,
				'province'=>$province,
				'district'=>$district,
				'subDistrict'=>$subDistrict,
				'disease'=>$disease
			]
		);
	}

	private function eventsAggregate($dateRange) {
		$totalCase = parent::countEvents($dateRange);
		$dcirCase = parent::countDcirCase($dateRange);
		$nonDcirCase = parent::countNonDcirCase();
		$dcirFinished = parent::countDcirFinishedCase();
		$agg['totalCase'] = $totalCase[0]->total_count;
		$agg['dcirCase'] = $dcirCase[0]->dcir_case;
		$agg['nonDcirCase'] = $nonDcirCase[0]->non_dcir_case;
		$agg['dcirFinished'] = $dcirFinished[0]->dcir_finished;
		$agg['dRange'] = $dateRange;
		return $agg;
	}

	private function getProvince($dcirData) {
		$provCode = $this->getProvCodeByCollect($dcirData);
		$provCode = $provCode->toArray();
		$prov = parent::provinceByCode($provCode);
		return $prov;
	}

	private function getProvCodeByCollect($dcirData) {
		$dcir = $dcirData;
		$provCode = collect();
		$dcir->each(function($item, $key) use ($provCode) {
			$provCode->push($item->province_id);
		});
		return $provCode;
	}

	private function getDistrict($dcirData) {
		$districtCode = $this->getDistrictCodeByCollect($dcirData);
		$districtCode = $districtCode->toArray();
		$dist = parent::districtByCode($districtCode);
		return $dist;
	}

	private function getDistrictCodeByCollect($dcirData) {
		$dcir = $dcirData;
		$districtCode = collect();
		$dcir->each(function($item, $key) use ($districtCode) {
			$districtCode->push($item->amphur_id);
		});
		return $districtCode;
	}

	private function getSubDistrict($dcirData) {
		$subDistrictCode = $this->getSubDistrictCodeByCollect($dcirData);
		$subDistrictCode = $subDistrictCode->toArray();
		$sdst = parent::subDistrictByCode($subDistrictCode);
		return $sdst;
	}

	private function getSubDistrictCodeByCollect($dcirData) {
		$dcir = $dcirData;
		$subDistrictCode = collect();
		$dcir->each(function($item, $key) use ($subDistrictCode) {
			$subDistrictCode->push($item->tambol_id);
		});
		return $subDistrictCode;
	}

	private function getDisease($dsList) {
		foreach ($dsList as $key=>$val) {
			$disease[$val->disease_id] = $val;
		}
		return $disease;
	}

	private function setNowDateRange() {
		$dNow = date('d');
		$mNow = date('m');
		$yNow = date('Y');
		return '01/01/'.$yNow.' - '.$dNow.'/'.$mNow.'/'.$yNow;
	}


}
