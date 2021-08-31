    var refreshSelect = function() {
        if (!element.select2Initialized) return;
        $timeout(function() {
            element.trigger('change');
        });
    };

    var change = true;

    var app = angular.module('delivrd-app',['ui.bootstrap', 'ui.bootstrap.modal', 'template/modal/backdrop.html', 'template/modal/window.html']);
    
    app.directive("select2", function($timeout, $parse) {
        return {
            restrict: 'AC',
            require: 'ngModel',
            link: function(scope, element, attrs) {
                $timeout(function() {
                    element.select2();
                    element.select2Initialized = true;
                });

                var refreshSelect = function() {
                    if (!element.select2Initialized) return;
                    $timeout(function() {
                        element.trigger('change');
                    });
                };
              
                var recreateSelect = function () {
                    if (!element.select2Initialized) return;
                    $timeout(function() {
                        element.select2('destroy');
                        element.select2();
                    });
                };

                scope.$watch(attrs.ngModel, refreshSelect);

                if (attrs.ngOptions) {
                    var list = attrs.ngOptions.match(/ in ([^ ]*)/)[1];
                    // watch for option list change
                    scope.$watch(list, recreateSelect);
                }

                if (attrs.ngDisabled) {
                    scope.$watch(attrs.ngDisabled, refreshSelect);
                }
            }
        };
    });

    app.directive("datepickercustom", function($timeout, $parse) {
        return {
            restrict: 'A',
            require : 'ngModel',
            link : function (scope, element, attrs, ngModelCtrl) {
                
                element.bind('change', function(e){
                    //triggered event if change
                    if(change) {
                        change = false;
                        scope.$apply(function () {
                            ngModelCtrl.$setViewValue(element.val());
                        });
                    }
                    setTimeout(function() {
                        change = true;
                    }, 100);
                });
            }
        }
    });


    angular.module("template/modal/backdrop.html", []).run(["$templateCache", function($templateCache) {
      $templateCache.put("template/modal/backdrop.html",
        "<div class=\"modal-backdrop fade\" ng-class=\"{in: animate}\" ng-style=\"{'z-index': 10040 + index*10}\" ng-click=\"close($event)\"></div>");
    }]);

    angular.module("template/modal/window.html", []).run(["$templateCache", function($templateCache) {
        $templateCache.put("template/modal/window.html",
        "<div class=\"modal fade {{ windowClass }}\" ng-class=\"{in: animate}\" style='display:block;z-index:10050'  ng-transclude></div>");
    }]);

    app.config(function ($httpProvider) {
        $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    });

    app.controller( 'ShipmentsList', ['$scope', '$http', '$modal', '$sce', function ($scope, $http, $modal, $sce) {
        /*$scope.popup = popup;
        $scope.is_write = is_write;
        $scope.ship_networks = ship_networks;*/

        $scope.networks = networks;

        $scope.status_id = status_id;
        $scope.direction_id = direction_id;
        $scope.createdfrom = createdfrom;
        $scope.tracking_number = tracking_number;

        $scope.currentPage = 1;
        $scope.limit = limit;

        $scope.sortBy = 'Shipment.modified';
        $scope.sortDir = 'DESC';

        $scope.showall = 0;

        $scope.shipments = [];
        getData();

        $scope.orderBy = function(field) {
            if(field == $scope.sortBy) {
                if($scope.sortDir == 'DESC') {
                    $scope.sortDir = 'ASC';
                } else {
                    $scope.sortDir = 'DESC';
                }
            } else {
                $scope.sortDir = 'ASC';
            }
            $scope.sortBy = field;
            
            getData();
        }
        
        $scope.orderDisplay = function(fileld) {
            var dir_class = 'text-muted';
            var direction = 'fa fa-sort';
            if(field == $scope.sortBy) {
                dir_class = 'text-info';
                if($scope.sortDir == 'ASC') {
                    direction = 'fa fa-sort-amount-asc';
                } else {
                    direction = 'fa fa-sort-amount-desc';
                }
            }
            return direction+' '+dir_class;
        }

        function getData() {
            var url = siteUrl + 'shipments/ajax_index';
            url = url +'/page:'+$scope.currentPage;
            url = url +'?limit='+$scope.limit;

            if($scope.direction_id) {
                url = url +'&direction_id='+$scope.direction_id;
            }

            if($scope.createdfrom != '') {
                url = url +'&createdfrom='+$scope.createdfrom;
            }

            if($scope.tracking_number != '') {
                url = url +'&tracking_number='+$scope.tracking_number;
            }

            if($scope.showall == 1) {
                url = url +'&showall=1';
            } else {
                if($scope.status_id != '') {
                    if($scope.status_id == 50) {
                        url = url +'&status_id='+$scope.status_id;
                    } else {
                        angular.forEach($scope.status_id, function(value,index){
                            url = url +'&status_id[]='+value;
                        });
                    }
                }
            }

            if($scope.sortBy != '') {
                url = url +'&sortby='+$scope.sortBy;
            }

            if($scope.sortDir != '') {
                url = url +'&sortdir='+$scope.sortDir;
            }
            
            $http.get(url)
            .then(function(response) {
                $scope.totalItems = response.data.recordsTotal;
                angular.copy(response.data.rows, $scope.shipments);
            }, function(response) {
                if(response.status == 403) {
                    window.location.href = siteUrl+'login';
                }
            });
        };

        $scope.applySearch = function() {
            $scope.currentPage = 1;
            getData();
            return false;
        }

        $scope.showAll = function() {
            $scope.status_id = '';
            $scope.direction_id = direction_id;
            $scope.createdfrom = '';
            $scope.tracking_number = '';
            $scope.currentPage = 1;
            $scope.showall = 1;
            $scope.limit = limit;

            $scope.sortBy = 'Shipment.modified';
            $scope.sortDir = 'DESC';
            
            getData();
            return false;
        }


        $scope.is_own = function(line) {
            if(userUid == line.Shipment.user_id) {
                return true;
            } else {
                return false;
            }
        }

        $scope.status = function($status_id) {
            var $status_text;
            if($status_id == '15') {
                $status_text = "<span class='label label-default'>Released</span>";
            } else if($status_id == '6') {
                $status_text = "<span class='label bg-yellow-gold'>Ready for Shipment</span>";
            } else if($status_id == '8') {
                $status_text = "<span class='label label-success'>Shipped</span>";
            }  else if($status_id == '7') {
                $status_text = "<span class='label label-success'>Fully Received</span>";
            } else if($status_id == '16') {
                $status_text = "<span class='label bg-yellow'>Partially Processed</span>";
            }
            return $sce.trustAsHtml($status_text);
        }

        $scope.formatDate = function(date){
            var dateOut = new Date(date);
            return dateOut;
        };

    }]);