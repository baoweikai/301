<?php
namespace traits;


/*
 * CCategoryBehavior class file.
 *
 * @author Weikai
 * @link http://www.yiiframework.com/
 * @copyright 2008-2013 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

trait Region
{
    use Cache;

    protected $country;
    protected $province;
    protected $city;
    protected $district;

    public $countryAttr = 'country';
    public $provinceAttr = 'province';
    public $cityAttr = 'city';
    public $districtAttr = 'district';
    public $townAttrAttr = 'townAttr';

    protected $countryName;
    protected $provinceName;
    protected $cityName;

    public function getCountry(){
        if(!empty($this->{$this->countryAttr})){
            return $this->{$this->countryAttr};
        }elseif(empty($this->{$this->provinceAttr})){
            return region::parentId($this->{$this->provinceAttr});
        }
    }

    public function setCountry($country){
        return $this->{$this->countryAttr} = $country;
    }

    public function getProvince(){
        if(!empty($this->{$this->provinceAttr})){
            return $this->{$this->provinceAttr};
        }elseif(empty($this->{$this->cityAttr})){
            return region::parentId($this->{$this->cityAttr});
        }
    }

    public function setProvince($province){
        return $this->{$this->provinceAttr} = $province;
    }

    public function getCity(){
        if(!empty($this->{$this->cityAttr})){
            return $this->{$this->cityAttr};
        }elseif(empty($this->{$this->districtAttr})){
            return region::parentId($this->{$this->districtAttr});
        }else{
            return $this->{$this->cityAttr};
        }
    }

    public function setCity($city){
        return $this->{$this->cityAttr} = $city;
    }

    public function getDistrict(){
        return $this->{$this->districtAttr};
    }

    public function setDistrict($district){
        return $this->{$this->districtAttr} = $district;
    }

    public function getCountryName(){
        if(!empty($this->{$this->countryAttr})){
            return region::name($this->{$this->countryAttr});
        }else{
            return '';
        }
    }

    public function getProvinceName(){
        if(!empty($this->{$this->provinceAttr})){
            return region::name($this->{$this->provinceAttr});
        }else{
            return '';
        }
    }

    public function getCityName(){
        if(!empty($this->{$this->cityAttr})){
            return region::name($this->{$this->cityAttr});
        }else{
            return '';
        }
    }

    public static function getFullname($id = 1, $full = ''){
        if(empty(self::$_rowList)){
            self::$_rowList = self::getRowList();
        }
        if(isset(self::$_rowList[$id]['name'])){
            $full = self::$_rowList[$id]['name'] .' ' . $full;
        }
        if(isset(self::$_rowList[$id]['parent_id']) &&  self::$_rowList[$id]['parent_id'] != 0){
            return self::getFullname(self::$_rowList[$id]['parent_id'], $full);
        }else{
            return $full;
        }
    }
}