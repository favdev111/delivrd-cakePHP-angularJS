<?php
App::uses('AppModel', 'Model');
/**
 * OrdersCosts Model
 *
 * @property Product $Product
 * @property Schannel $Schannel
 */
class Forecast extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'forecasts';

    public $actsAs = array(
        'Containable'
    );

    public $recursive = 0;

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'product_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Prdduct is required.',
                'required' => true,
            ),
        ),
        'forecast' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Amount must be numeric',
                'required' => true,
                'on' => 'create',
            ),
            'positive' => array(
                'rule' => array('comparison', '>=', 0),
                'message' => 'Amount must be greater than zero', 
            ),
        ),
    );

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'Product' => array(
            'className' => 'Product',
            'foreignKey' => 'product_id',
        )
    );
    
    public function beforeSave($options = array()) {
        return true;
    }

    public function afterSave($created, $options = array()) {
        return true;
    }
    
    public function getMonthlyForecast($product_id, $default_forecast = 0) {
        $today = date('Y-m');
        $year = [];
        for($i = 0; $i <= 11; $i++) {
            $m = date('Y-m', strtotime($today .' + '. $i .' month'));
            $lines[$m] = [];

            $this->virtualFields['month'] = 'DATE_FORMAT(Forecast.forecast_date, "%Y-%m")';
            $forecast = $this->find('first', [
                'contain' => false,
                'conditions' => [
                    'Forecast.period' => 'month',
                    'Forecast.product_id' => $product_id,
                    'Forecast.month' => $m
                ],
                'fields' => [
                    'Forecast.forecast'
                ]
            ]);
            $lines[$m]['forecast'] = isset($forecast['Forecast']['forecast'])?$forecast['Forecast']['forecast'] : $default_forecast;
            $lines[$m]['month'] = $m;
        }
        return $lines;
    }
}