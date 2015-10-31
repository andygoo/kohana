<?php

class Chart {
    
    public $colors = array();
    public $colors2 = array();
    
    public $code;

    function __construct() {
        $this->colors1 = array(
            'rgb(220,220,220)','rgb(151,187,205)', 
        );
        $this->colors2 = array(
            '#f9766e', '#ce9500', '#7cae00', '#00a8ff', '#c77bff', '#fe61cc',
            '#5B90BF', '#96b5b4', '#a3be8c', '#ab7967', '#d08770', '#b48ead', 
            '#F7464A', '#46BFBD', '#FDB45C', '#949FB1', '#4D5360', '#deafb8',
        );
        $chart_js = URL::site('media/js/Chart.js');
        $this->code = <<<EOF
        <script>
        function LoadChartJs() {
            var head = document.getElementsByTagName('head')[0];
            var chartjs = document.createElement('script');
            chartjs.src = '$chart_js';
            head.appendChild(chartjs);
        }
        if(typeof window['Chart'] == 'undefined') {
            LoadChartJs();
        }
        </script>\n
EOF;
    }
    
    public function line($datas, $width=600, $height=400) {
        $chart_data = self::getChartData($datas);
        $id = 'canvas_line_' . mt_rand(1000, 9999);
        return $this->code .= <<<EOF
        <canvas id="$id" width="$width" height="$height"></canvas>
        <script>
        setTimeout(function() {
        	var ctx = document.getElementById("$id").getContext("2d");
        	new Chart(ctx).Line($chart_data);
        },200);
        </script>\n
EOF;
    } 
    
    public function bar($datas, $width=600, $height=400) {
        $chart_data = $this->getChartData($datas);
        $id = 'canvas_bar_' . mt_rand(1000, 9999);
        return $this->code .= <<<EOF
        <canvas id="$id" width="$width" height="$height"></canvas>
        <script>
        setTimeout(function(){
        	var ctx = document.getElementById("$id").getContext("2d");
        	new Chart(ctx).Bar($chart_data);
        },200);
        </script>\n
EOF;
    }
    
    public function radar($datas, $width=400, $height=400) {
        $chart_data = $this->getChartData($datas);
        $id = 'canvas_radar_' . mt_rand(1000, 9999);
        return $this->code .= <<<EOF
        <canvas id="$id" width="$width" height="$height"></canvas>
        <script>
        setTimeout(function(){
        	var ctx = document.getElementById("$id").getContext("2d");
        	new Chart(ctx).Radar($chart_data);
        },200);
        </script>\n
EOF;
    }
    
    public function pie($data, $width=300, $height=300) {
        $chart_data = $this->getChartData2($data);
        $id = 'canvas_pie_' . mt_rand(1000, 9999);
        return $this->code .= <<<EOF
        <canvas id="$id" width="$width" height="$height"></canvas>
        <script>
        setTimeout(function(){
        	var ctx = document.getElementById("$id").getContext("2d");
        	new Chart(ctx).Pie($chart_data);
        },200);
        </script>\n
EOF;
    }

    public function doughnut($data, $width=300, $height=300) {
        $chart_data = $this->getChartData2($data);
        $id = 'canvas_doughnut_' . mt_rand(1000, 9999);
        return $this->code .= <<<EOF
        <canvas id="$id" width="$width" height="$height"></canvas>
        <script>
        setTimeout(function(){
        	var ctx = document.getElementById("$id").getContext("2d");
        	new Chart(ctx).Doughnut($chart_data);
        },200);
        </script>\n
EOF;
    }
    
    public function polararea($data, $width=300, $height=300) {
        $chart_data = $this->getChartData2($data);
        $id = 'canvas_polararea_' . mt_rand(1000, 9999);
        return $this->code .= <<<EOF
        <canvas id="$id" width="$width" height="$height"></canvas>
        <script>
        setTimeout(function(){
        	var ctx = document.getElementById("$id").getContext("2d");
        	new Chart(ctx).PolarArea($chart_data);
        },200);
        </script>\n
EOF;
    }
    
    protected function getColorOptions($color) {
        list($r, $g, $b) = sscanf($color, 'rgb(%d, %d, %d)');
        
        $a20 = "rgba($r, $g, $b, 0.2)";
        $a50 = "rgba($r, $g, $b, 0.5)";
        $a75 = "rgba($r, $g, $b, 0.75)";
        $a80 = "rgba($r, $g, $b, 0.8)";
        $a100 = "rgba($r, $g, $b, 1)";

        $color_options = array();
        $color_options['fillColor'] = $a20;
        $color_options['strokeColor'] = $a100;
        $color_options['highlightFill'] = $a75;
        $color_options['highlightStroke'] = $a100;
        $color_options['pointColor'] = $a100;
        $color_options['pointStrokeColor'] = '#fff';
        $color_options['pointHighlightFill'] = '#fff';
        $color_options['pointHighlightStroke'] = $a100;
        return $color_options;
    }
    
    protected function getChartData($datas) {
        $colors = $this->colors1;
        foreach ($datas as $data) {
            $color = array_shift($colors);
            $datasets = $this->getColorOptions($color);
            $datasets['data'] = array_values($data);
            $chartData['datasets'][] = $datasets;
            $chartData['labels'] = array_keys($data);
        }
        return json_encode($chartData);
    }
    
    protected function getChartData2($data) {
        $colors = $this->colors2;
        $chartData = array();
        foreach ($data as $label => $value) {
            if (count($colors) < 1) {
                $r = rand(0,255); $g = rand(0,255); $b = rand(0,255);
            } else {
                $color = array_shift($colors);
                list($r, $g, $b) = $this->col2rgb($color);
            }
            $chartData[] = array(
                'label' => $label,
                'value' => $value,
                'color' => "rgba($r, $g, $b, 1)",
                'highlight' => "rgba($r, $g, $b, 0.75)",
            );
        }
        return json_encode($chartData);
    }
    
    protected function col2rgb($color) {
        $hex = str_replace('#', '', $color);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return array($r, $g, $b);
    }
}
