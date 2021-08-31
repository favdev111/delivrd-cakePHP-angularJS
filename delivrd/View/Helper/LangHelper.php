<?php  
/** 
 * Outputs a country select list and/or a language select list. Automatically 
 * detects language and country codes from browser headers. 
 * 
 * Usage... 
 * 
 *   echo $lang->countrySelect('Foo.country'); 
 *   echo $lang->languageSelect('Foo.language'); 
 * 
 * You can override defaults such as: 
 * 
 *   echo $lang->countrySelect('Foo.country', array( 
 *     'label' => __('Choose a Country', true), 
 *     'default' => 'ru', 
 *      'class' => 'some-class' 
 *   )); 
 * 
 *   echo $lang->languageSelect('Foo.language', array( 
 *     'label' => __('Choose a Language', true), 
 *     'default' => 'sp', 
 *     'class' => 'some-class' 
 *   )); 
 * 
 * Note that the 'default' option is only used if the form was not previously 
 * submitted, and country/language information could not be extracted from 
 * the HTTP request. 
 * 
 * @note Some snippets taken from Tane Piper <digitalspaghetti@gmail.com> 
 * @see http://digitalspaghetti.me.uk 
 * 
 * @author Brendon Crawford 
 * @see http://aphexcreations.net 
 * 
 * @license 
 *   Licensed under The MIT License 
 *   Redistributions of files must retain the above copyright notice. 
 * 
 */ 
App::uses('FormHelper', 'View/Helper');

class LangHelper extends FormHelper { 

    public $helpers = array('Form'); 
    private $mapper = array(); 

    private $countries = array( 
        "AF" => "Afghanistan",
"AL" => "Albania",
"DZ" => "Algeria",
"AS" => "American Samoa",
"AD" => "Andorra",
"AO" => "Angola",
"AI" => "Anguilla",
"AQ" => "Antarctica",
"AG" => "Antigua and Barbuda",
"AR" => "Argentina",
"AM" => "Armenia",
"AW" => "Aruba",
"AU" => "Australia",
"AT" => "Austria",
"AZ" => "Azerbaijan",
"BS" => "Bahamas",
"BH" => "Bahrain",
"BD" => "Bangladesh",
"BB" => "Barbados",
"BY" => "Belarus",
"BE" => "Belgium",
"BZ" => "Belize",
"BJ" => "Benin",
"BM" => "Bermuda",
"BT" => "Bhutan",
"BO" => "Bolivia",
"BA" => "Bosnia and Herzegovina",
"BW" => "Botswana",
"BV" => "Bouvet Island",
"BR" => "Brazil",
"BQ" => "British Antarctic Territory",
"IO" => "British Indian Ocean Territory",
"VG" => "British Virgin Islands",
"BN" => "Brunei",
"BG" => "Bulgaria",
"BF" => "Burkina Faso",
"BI" => "Burundi",
"KH" => "Cambodia",
"CM" => "Cameroon",
"CA" => "Canada",
"CT" => "Canton and Enderbury Islands",
"CV" => "Cape Verde",
"KY" => "Cayman Islands",
"CF" => "Central African Republic",
"TD" => "Chad",
"CL" => "Chile",
"CN" => "China",
"CX" => "Christmas Island",
"CC" => "Cocos [Keeling] Islands",
"CO" => "Colombia",
"KM" => "Comoros",
"CG" => "Congo - Brazzaville",
"CD" => "Congo - Kinshasa",
"CK" => "Cook Islands",
"CR" => "Costa Rica",
"HR" => "Croatia",
"CU" => "Cuba",
"CY" => "Cyprus",
"CZ" => "Czech Republic",
"CI" => "Côte d’Ivoire",
"DK" => "Denmark",
"DJ" => "Djibouti",
"DM" => "Dominica",
"DO" => "Dominican Republic",
"NQ" => "Dronning Maud Land",
"DD" => "East Germany",
"EC" => "Ecuador",
"EG" => "Egypt",
"SV" => "El Salvador",
"GQ" => "Equatorial Guinea",
"ER" => "Eritrea",
"EE" => "Estonia",
"ET" => "Ethiopia",
"FK" => "Falkland Islands",
"FO" => "Faroe Islands",
"FJ" => "Fiji",
"FI" => "Finland",
"FR" => "France",
"GF" => "French Guiana",
"PF" => "French Polynesia",
"TF" => "French Southern Territories",
"FQ" => "French Southern and Antarctic Territories",
"GA" => "Gabon",
"GM" => "Gambia",
"GE" => "Georgia",
"DE" => "Germany",
"GH" => "Ghana",
"GI" => "Gibraltar",
"GR" => "Greece",
"GL" => "Greenland",
"GD" => "Grenada",
"GP" => "Guadeloupe",
"GU" => "Guam",
"GT" => "Guatemala",
"GG" => "Guernsey",
"GN" => "Guinea",
"GW" => "Guinea-Bissau",
"GY" => "Guyana",
"HT" => "Haiti",
"HM" => "Heard Island and McDonald Islands",
"HN" => "Honduras",
"HK" => "Hong Kong SAR China",
"HU" => "Hungary",
"IS" => "Iceland",
"IN" => "India",
"ID" => "Indonesia",
"IR" => "Iran",
"IQ" => "Iraq",
"IE" => "Ireland",
"IM" => "Isle of Man",
"IL" => "Israel",
"IT" => "Italy",
"JM" => "Jamaica",
"JP" => "Japan",
"JE" => "Jersey",
"JT" => "Johnston Island",
"JO" => "Jordan",
"KZ" => "Kazakhstan",
"KE" => "Kenya",
"KI" => "Kiribati",
"KW" => "Kuwait",
"KG" => "Kyrgyzstan",
"LA" => "Laos",
"LV" => "Latvia",
"LB" => "Lebanon",
"LS" => "Lesotho",
"LR" => "Liberia",
"LY" => "Libya",
"LI" => "Liechtenstein",
"LT" => "Lithuania",
"LU" => "Luxembourg",
"MO" => "Macau SAR China",
"MK" => "Macedonia",
"MG" => "Madagascar",
"MW" => "Malawi",
"MY" => "Malaysia",
"MV" => "Maldives",
"ML" => "Mali",
"MT" => "Malta",
"MH" => "Marshall Islands",
"MQ" => "Martinique",
"MR" => "Mauritania",
"MU" => "Mauritius",
"YT" => "Mayotte",
"FX" => "Metropolitan France",
"MX" => "Mexico",
"FM" => "Micronesia",
"MI" => "Midway Islands",
"MD" => "Moldova",
"MC" => "Monaco",
"MN" => "Mongolia",
"ME" => "Montenegro",
"MS" => "Montserrat",
"MA" => "Morocco",
"MZ" => "Mozambique",
"MM" => "Myanmar [Burma]",
"NA" => "Namibia",
"NR" => "Nauru",
"NP" => "Nepal",
"NL" => "Netherlands",
"AN" => "Netherlands Antilles",
"NT" => "Neutral Zone",
"NC" => "New Caledonia",
"NZ" => "New Zealand",
"NI" => "Nicaragua",
"NE" => "Niger",
"NG" => "Nigeria",
"NU" => "Niue",
"NF" => "Norfolk Island",
"KP" => "North Korea",
"VD" => "North Vietnam",
"MP" => "Northern Mariana Islands",
"NO" => "Norway",
"OM" => "Oman",
"PC" => "Pacific Islands Trust Territory",
"PK" => "Pakistan",
"PW" => "Palau",
"PS" => "Palestinian Territories",
"PA" => "Panama",
"PZ" => "Panama Canal Zone",
"PG" => "Papua New Guinea",
"PY" => "Paraguay",
"YD" => "People's Democratic Republic of Yemen",
"PE" => "Peru",
"PH" => "Philippines",
"PN" => "Pitcairn Islands",
"PL" => "Poland",
"PT" => "Portugal",
"PR" => "Puerto Rico",
"QA" => "Qatar",
"RO" => "Romania",
"RU" => "Russia",
"RW" => "Rwanda",
"RE" => "Réunion",
"BL" => "Saint Barthélemy",
"SH" => "Saint Helena",
"KN" => "Saint Kitts and Nevis",
"LC" => "Saint Lucia",
"MF" => "Saint Martin",
"PM" => "Saint Pierre and Miquelon",
"VC" => "Saint Vincent and the Grenadines",
"WS" => "Samoa",
"SM" => "San Marino",
"SA" => "Saudi Arabia",
"SN" => "Senegal",
"RS" => "Serbia",
"CS" => "Serbia and Montenegro",
"SC" => "Seychelles",
"SL" => "Sierra Leone",
"SG" => "Singapore",
"SK" => "Slovakia",
"SI" => "Slovenia",
"SB" => "Solomon Islands",
"SO" => "Somalia",
"ZA" => "South Africa",
"GS" => "South Georgia and the South Sandwich Islands",
"KR" => "South Korea",
"ES" => "Spain",
"LK" => "Sri Lanka",
"SD" => "Sudan",
"SR" => "Suriname",
"SJ" => "Svalbard and Jan Mayen",
"SZ" => "Swaziland",
"SE" => "Sweden",
"CH" => "Switzerland",
"SY" => "Syria",
"ST" => "São Tomé and Príncipe",
"TW" => "Taiwan",
"TJ" => "Tajikistan",
"TZ" => "Tanzania",
"TH" => "Thailand",
"TL" => "Timor-Leste",
"TG" => "Togo",
"TK" => "Tokelau",
"TO" => "Tonga",
"TT" => "Trinidad and Tobago",
"TN" => "Tunisia",
"TR" => "Turkey",
"TM" => "Turkmenistan",
"TC" => "Turks and Caicos Islands",
"TV" => "Tuvalu",
"UM" => "U.S. Minor Outlying Islands",
"PU" => "U.S. Miscellaneous Pacific Islands",
"VI" => "U.S. Virgin Islands",
"UG" => "Uganda",
"UA" => "Ukraine",
"SU" => "Union of Soviet Socialist Republics",
"AE" => "United Arab Emirates",
"GB" => "United Kingdom",
"US" => "United States",
"ZZ" => "Unknown or Invalid Region",
"UY" => "Uruguay",
"UZ" => "Uzbekistan",
"VU" => "Vanuatu",
"VA" => "Vatican City",
"VE" => "Venezuela",
"VN" => "Vietnam",
"WK" => "Wake Island",
"WF" => "Wallis and Futuna",
"EH" => "Western Sahara",
"YE" => "Yemen",
"ZM" => "Zambia",
"ZW" => "Zimbabwe",
"AX" => "Åland Islands"
    ); 
	
	private $states = array( 
        'al' =>    'Alabama', 
        'ny' =>    'New York', 
		 );

    private $languages = array( 
        'ab' => 'Abkhazian', 
        'aa' => 'Afar', 
        'af' => 'Afrikaans', 
        'ak' => 'Akan', 
        'sq' => 'Albanian', 
        'am' => 'Amharic', 
        'ar' => 'Arabic', 
        'an' => 'Aragonese', 
        'hy' => 'Armenian', 
        'as' => 'Assamese', 
        'av' => 'Avaric', 
        'ae' => 'Avestan', 
        'ay' => 'Aymara', 
        'az' => 'Azerbaijani', 
        'bm' => 'Bambara', 
        'ba' => 'Bashkir', 
        'eu' => 'Basque', 
        'be' => 'Belarusian', 
        'bn' => 'Bengali', 
        'bh' => 'Bihari', 
        'bi' => 'Bislama', 
        'nb' => 'Bokmal', 
        'bs' => 'Bosnian', 
        'br' => 'Breton', 
        'bg' => 'Bulgarian', 
        'my' => 'Burmese', 
        'ca' => 'Catalan', 
        'km' => 'Central Khmer', 
        'ch' => 'Chamorro', 
        'ce' => 'Chechen', 
        'ny' => 'Chewa', 
        'zh' => 'Chinese', 
        'cu' => 'Church Slavic', 
        'cv' => 'Chuvash', 
        'kw' => 'Cornish', 
        'co' => 'Corsican', 
        'cr' => 'Cree', 
        'hr' => 'Croatian', 
        'cs' => 'Czech', 
        'da' => 'Danish', 
        'dv' => 'Dhivehi', 
        'nl' => 'Dutch', 
        'dz' => 'Dzongkha', 
        'en' => 'English', 
        'eo' => 'Esperanto', 
        'et' => 'Estonian', 
        'ee' => 'Ewe', 
        'fo' => 'Faroese', 
        'fj' => 'Fijian', 
        'fi' => 'Finnish', 
        'fr' => 'French', 
        'ff' => 'Fulah', 
        'gd' => 'Gaelic', 
        'gl' => 'Galician', 
        'lg' => 'Ganda', 
        'ka' => 'Georgian', 
        'de' => 'German', 
        'ki' => 'Gikuyu', 
        'el' => 'Greek', 
        'kl' => 'Greenlandic', 
        'gn' => 'Guarani', 
        'gu' => 'Gujarati', 
        'ht' => 'Haitian', 
        'ha' => 'Hausa', 
        'he' => 'Hebrew', 
        'hz' => 'Herero', 
        'hi' => 'Hindi', 
        'ho' => 'Hiri Motu', 
        'hu' => 'Hungarian', 
        'is' => 'Icelandic', 
        'io' => 'Ido', 
        'ig' => 'Igbo', 
        'id' => 'Indonesian', 
        'ia' => 'Interlingua', 
        'iu' => 'Inuktitut', 
        'ik' => 'Inupiaq', 
        'ga' => 'Irish', 
        'it' => 'Italian', 
        'ja' => 'Japanese', 
        'jv' => 'Javanese', 
        'kn' => 'Kannada', 
        'kr' => 'Kanuri', 
        'ks' => 'Kashmiri', 
        'kk' => 'Kazakh', 
        'rw' => 'Kinyarwanda', 
        'kv' => 'Komi', 
        'kg' => 'Kongo', 
        'ko' => 'Korean', 
        'ku' => 'Kurdish', 
        'kj' => 'Kwanyama', 
        'ky' => 'Kyrgyz', 
        'lo' => 'Lao', 
        'la' => 'Latin', 
        'lv' => 'Latvian', 
        'lb' => 'Letzeburgesch', 
        'li' => 'Limburgan', 
        'ln' => 'Lingala', 
        'lt' => 'Lithuanian', 
        'lu' => 'Luba-Katanga', 
        'mk' => 'Macedonian', 
        'mg' => 'Malagasy', 
        'ms' => 'Malay', 
        'ml' => 'Malayalam', 
        'mt' => 'Maltese', 
        'gv' => 'Manx', 
        'mi' => 'Maori', 
        'mr' => 'Marathi', 
        'mh' => 'Marshallese', 
        'ro' => 'Moldavian', 
        'mn' => 'Mongolian', 
        'na' => 'Nauru', 
        'nv' => 'Navajo', 
        'ng' => 'Ndonga', 
        'ne' => 'Nepali', 
        'nd' => 'North Ndebele', 
        'se' => 'Northern Sami', 
        'no' => 'Norwegian', 
        'nn' => 'Norwegian Nynorsk', 
        'ie' => 'Occidental', 
        'oc' => 'Occitan', 
        'oj' => 'Ojibwa', 
        'or' => 'Oriya', 
        'om' => 'Oromo', 
        'os' => 'Ossetian', 
        'pi' => 'Pali', 
        'fa' => 'Persian', 
        'pl' => 'Polish', 
        'pt' => 'Portuguese', 
        'pa' => 'Punjabi', 
        'ps' => 'Pushto', 
        'qu' => 'Quechua', 
        'ro' => 'Romanian', 
        'rm' => 'Romansh', 
        'rn' => 'Rundi', 
        'ru' => 'Russian', 
        'sm' => 'Samoan', 
        'sg' => 'Sango', 
        'sa' => 'Sanskrit', 
        'sc' => 'Sardinian', 
        'sr' => 'Serbian', 
        'sn' => 'Shona', 
        'ii' => 'Sichuan Yi', 
        'sd' => 'Sindhi', 
        'si' => 'Sinhalese', 
        'sk' => 'Slovak', 
        'sl' => 'Slovenian', 
        'so' => 'Somali', 
        'st' => 'Southern Sotho', 
        'nr' => 'South Ndebele', 
        'es' => 'Spanish', 
        'su' => 'Sundanese', 
        'sw' => 'Swahili', 
        'ss' => 'Swati', 
        'sv' => 'Swedish', 
        'tl' => 'Tagalog', 
        'ty' => 'Tahitian', 
        'tg' => 'Tajik', 
        'ta' => 'Tamil', 
        'tt' => 'Tatar', 
        'te' => 'Telugu', 
        'th' => 'Thai', 
        'bo' => 'Tibetan', 
        'ti' => 'Tigrinya', 
        'to' => 'Tonga', 
        'ts' => 'Tsonga', 
        'tn' => 'Tswana', 
        'tr' => 'Turkish', 
        'tk' => 'Turkmen', 
        'tw' => 'Twi', 
        'uk' => 'Ukrainian', 
        'ur' => 'Urdu', 
        'ug' => 'Uyghur', 
        'uz' => 'Uzbek', 
        've' => 'Venda', 
        'vi' => 'Vietnamese', 
        'vo' => 'Volapük', 
        'wa' => 'Walloon', 
        'cy' => 'Welsh', 
        'fy' => 'Western Frisian', 
        'wo' => 'Wolof', 
        'xh' => 'Xhosa', 
        'yi' => 'Yiddish', 
        'yo' => 'Yoruba', 
        'za' => 'Zhuang', 
        'zu' => 'Zulu' 
    ); 

    private $defaultLang = 'en'; 
    private $defaultCountry = 'us'; 
    private $langCode = null; 
    private $countryCode = null; 

    /** 
     * @constructor 
     */ 
//    public function __construct() { 
//        $this->mapper = $this->parseLangHeaders(); 
 //       $this->langCode = $this->findLangCode(); 
 //       $this->countryCode = $this->findCountryCode(); 
 //   } 
 
 public function __construct(View $View, $settings = array()) {
    parent::__construct($View, $settings); 
    $this->mapper = $this->parseLangHeaders();
    $this->langCode = $this->findLangCode();
    $this->countryCode = $this->findCountryCode();
}

    /** 
     * Sets Defaults 
     * 
     * @param string $lang 
     * @param string|null $country optional 
     * @return bool 
     */ 
    public function setDefaults($lang, $country=null) { 
        $this->defaultLang = $lang; 
        if ($country !== null) { 
            $this->defaultCountry = $country; 
        } 
        return true; 
    } 

    /** 
     * Finds Lang Code 
     * 
     * @return string|null 
     */ 
    private function findLangCode() { 
        reset($this->mapper); 
        $f = current($this->mapper); 
        if ($f === false) { 
            return null; 
        } 
        else { 
            return $f->language; 
        } 
    } 

    /** 
     * Finds Country Code 
     * 
     * @return string|null 
     */ 
    private function findCountryCode() { 
        reset($this->mapper); 
        foreach ($this->mapper as $map) { 
            if ($map->country !== null) { 
                return $map->country; 
            } 
        } 
        return null; 
    } 
	
	private function findStateCode() { 
        reset($this->mapper); 
        foreach ($this->mapper as $map) { 
            if ($map->state !== null) { 
                return $map->state; 
            } 
        } 
        return null; 
    } 

    /** 
     * Parses HTTP Request Language Headers 
     * 
     * @param string $accept 
     * @return array 
     */ 
    private function parseLangHeaders($accept=null) { 
        if ($accept === null) { 
            $langHead = env('HTTP_ACCEPT_LANGUAGE'); 
        } 
        else { 
            $langHead = (string)$accept; 
        } 
        $langs = preg_split('/\s*,\s*/i', $langHead, -1, PREG_SPLIT_NO_EMPTY); 
        $out = array(); 
        $i = 0; 
        $weightIndex = 1; 
        foreach ($langs as $lang) { 
            $opts = preg_split('/\s*;\s*/i', $lang, -1, PREG_SPLIT_NO_EMPTY); 
            $code = $opts[0]; 
            $weight = null; 
            $codeSegs = explode('-', $code); 
            $langCode = strtolower($codeSegs[0]); 
            $ctryCode = null; 
            if (array_key_exists(1, $codeSegs)) { 
                $ctryCode = strtolower($codeSegs[1]); 
            } 
            if (array_key_exists(1, $opts)) { 
                $qParams = explode('=', $opts[1]); 
                if ($qParams[0] === 'q') { 
                    if (array_key_exists(1, $qParams) && is_numeric($qParams[1])) { 
                        $weight = (float)$qParams[1]; 
                    } 
                } 
            } 
            if ($weight === null) { 
                $weight = $weightIndex; 
            } 
            $out[] = (object)array( 
                'language' => $langCode, 
                'country' => $ctryCode, 
                'weight' => $weight 
            ); 
            $i++; 
            if ($weightIndex > 0) { 
                $weightIndex -= .1; 
            } 
        } 
        uasort($out, array($this, 'weightSort')); 
        return $out; 
    } 

    /** 
     * Sorts request lang code weights 
     * 
     * @param object $a 
     * @param object $b 
     * @return int 
     */ 
    public function weightSort($a, $b) { 
        if ($a->weight === $b->weight) { 
            return 0; 
        } 
        elseif ($a->weight > $b->weight) { 
            return -1; 
        } 
        else { 
            return 1; 
        } 
    } 

    /** 
     * Finds selected element 
     * 
     * @param string $fieldName 
     * @return assoc 
     */ 
	 /** 
     * Finds selected element 
     * 
     * @param string $fieldName 
     * @return assoc 
     */ 
    private function getSelected($fieldName) { 
        if (empty($this->data) || ! is_array($this->data)) { 
            return null; 
        }else{ 
            $formDatas = reset($this->data); 
            if ( array_key_exists($fieldName, $formDatas) ){ 
                return reset($formDatas[$fieldName]); 
            } 
        } 
     
        $view =& ClassRegistry::getObject('view'); 
        $this->setEntity($fieldName); 
        $ent = $view->entity(); 
        if (empty($ent)) { 
            return null; 
        } 
        $obj = $this->data; 
        $i = 0; 
        while (true) { 
            if (is_array($obj)) { 
                if (array_key_exists($ent[$i], $obj)) { 
                    $obj = $obj[$ent[$i]]; 
                    $i++; 
                } 
            } 
            else { 
                return $obj; 
            } 
        } 
    } 

    /** 
     * Outputs country list 
     * 
     * @param string $fieldName 
     * @param assoc $options 
     * @return string 
     */ 
    public function countrySelect($fieldName, $options=array()) { 
        $options = array_merge(array( 
            'label' => __('Country', true), 
            'default' => $this->defaultCountry, 
            'class' => null 
        ), $options); 
        $selected = $this->getSelected($fieldName); 
        if ($selected === null || 
                !array_key_exists($selected, $this->countries)) { 
            if ($this->countryCode === null) { 
                $selected = $options['default']; 
            } 
            else { 
                $selected = $this->countryCode; 
            } 
        } 
        $opts = array(); 
        $opts['options'] = $this->countries; 
        $opts['selected'] = $selected; 
        $opts['multiple'] = false; 
        $opts['label'] = $options['label']; 
        if ($options['class'] !== null) { 
            $opts['class'] = $options['class']; 
        } 
        $out = $this->Form->input($fieldName, $opts); 
        return $this->output($out); 
    } 
	
	public function stateSelect($fieldName, $options=array()) { 
        $options = array_merge(array( 
            'label' => __('State', true), 
            'default' => $this->defaultState, 
            'class' => null 
        ), $options); 
        $selected = $this->getSelected($fieldName); 
        if ($selected === null || 
                !array_key_exists($selected, $this->states)) { 
            if ($this->stateCode === null) { 
                $selected = $options['default']; 
            } 
            else { 
                $selected = $this->stateCode; 
            } 
        } 
        $opts = array(); 
        $opts['options'] = $this->states; 
        $opts['selected'] = $selected; 
        $opts['multiple'] = false; 
        $opts['label'] = $options['label']; 
        if ($options['class'] !== null) { 
            $opts['class'] = $options['class']; 
        } 
        $out = $this->Form->input($fieldName, $opts); 
        return $this->output($out); 
    } 

    /** 
     * Outputs language list 
     * 
     * @param string $fieldName 
     * @param assoc $options 
     * @return string 
     */ 
    public function languageSelect($fieldName, $options=array()) { 
        $options = array_merge(array( 
            'label' => __('Language', true), 
            'default' => $this->defaultLang, 
            'class' => null 
        ), $options); 
        $selected = $this->getSelected($fieldName); 
        if ($selected === null || 
                !array_key_exists($selected, $this->languages)) { 
            if ($this->langCode === null) { 
                $selected = $options['default']; 
            } 
            else { 
                $selected = $this->langCode; 
            } 
        } 
        $opts = array(); 
        $opts['options'] = $this->languages; 
        $opts['selected'] = $selected; 
        $opts['multiple'] = false; 
        $opts['label'] = $options['label']; 
        if ($options['class'] !== null) { 
            $opts['class'] = $options['class']; 
        } 
        $out = $this->Form->input($fieldName, $opts); 
        return $this->output($out); 
    } 

} 
?>