var app = angular.module('delivrd-app',['ui.bootstrap', 'template/modal/backdrop.html', 'template/modal/window.html']);

angular.module("template/modal/backdrop.html", []).run(["$templateCache", function($templateCache) {
  $templateCache.put("template/modal/backdrop.html",
    "<div class=\"modal-backdrop fade\" ng-class=\"{in: animate}\" ng-style=\"{'z-index': 10040 + index*10}\" ng-click=\"close($event)\"></div>");
}]);

angular.module("template/modal/window.html", []).run(["$templateCache", function($templateCache) {
    $templateCache.put("template/modal/window.html",
    "<div class=\"modal fade {{ windowClass }}\" ng-class=\"{in: animate}\" style='display:block;z-index:10050'  ng-transclude></div>");
}]);

app.controller( 'AddProduct', ['$scope', '$http', '$modal', function ($scope, $http, $modal) {
    $scope.channel_prices = channel_prices;
    $scope.schannels = schannels;
    $scope.schannels_a = schannels_a;
    
    $scope.parts = parts;
    $scope.product_parts = product_parts;
    $scope.parts_a = parts_a;

    $scope.addKit = function() {
        if($scope.parts_id == undefined || $scope.parts_id == '') {
            toastr.error('Please select product.');
            return false;
        }
        if($scope.quantity == undefined) {
            toastr.error('Please enter quantity.');
            return false;
        }
        var is_new = true;
        angular.forEach($scope.product_parts, function(value,index){
            if(value.Kit.parts_id == $scope.parts_id.id) {
                is_new = false;
                $scope.product_parts[index].Kit.quantity = $scope.quantity;
                $scope.product_parts[index].Kit.active = $scope.active;
            }
        });

        if(is_new) {
            var new_part = {
                'Kit': {
                    'id': Math.random().toString(36).slice(2),
                    'parts_id': parseInt($scope.parts_id.id),
                    'quantity': $scope.quantity,
                    'active': $scope.active
                }
            };
            $scope.product_parts.unshift(new_part);
        }

        $scope.parts_id = '';
        $scope.quantity = '';
    }

    $scope.removeKit = function($id) {
        angular.forEach($scope.product_parts, function(value,index){
            if(value.Kit.id == $id) {
                $scope.product_parts.splice(index,1);
                $scope.product_parts.splice(index,0);
            }
        });
    }

    $scope.removePrice = function($id) {
        angular.forEach($scope.channel_prices, function(value,index){
            if(value.ProductsPrices.id == $id) {
                $scope.channel_prices.splice(index,1);
                $scope.channel_prices.splice(index,0);
            }
        });
    }

    $scope.addPrice = function() {

        //var price = parseFloat($scope.schannel_price);
        
        if($scope.schannel_price == undefined || $scope.schannel_price <= 0) {
            toastr.error('Please enter valid Price.');
            return false;
        }
        if($scope.schannel_id == undefined) {
            toastr.error('Please select Channel.');
            return false;
        }
        var is_new = true;
        angular.forEach($scope.channel_prices, function(value,index){
            if(value.ProductsPrices.schannel_id == $scope.schannel_id.id) {
                is_new = false;
                $scope.channel_prices[index].ProductsPrices.value = $scope.schannel_price;
            }
        });

        if(is_new) {
            var new_price = {
                'ProductsPrices': {
                    'id': Math.random().toString(36).slice(2),
                    'schannel_id': parseInt($scope.schannel_id.id),
                    'value': $scope.schannel_price
                }
            };
            $scope.channel_prices.unshift(new_price);
        }
    }

    // Add Sales Channel
    $scope.addSchannel = function() {
        var modalAdditionalCostsInstance = $modal.open({
            templateUrl: siteUrl+'schannels/add_channel?bust='+Math.random().toString(36).slice(2),
            controller: ModalAddSchannelInstanceCtrl
        });

        modalAdditionalCostsInstance.result.then(function (data) {
            if(data.action == 'success') {
                
                $scope.schannels[data.schannel.Schannel.id] = data.schannel.Schannel.name;
                var new_item = {'id': data.schannel.Schannel.id, 'name': data.schannel.Schannel.name};
                $scope.schannels_a.push(new_item);
                $scope.schannel_id = new_item;
                
                toastr["success"](data.message);
            } else {
                toastr["error"](data.message);
            }
        });
    }

    var ModalAddSchannelInstanceCtrl = function($scope, $modalInstance, $http) {

        $scope.addChannel = function (e) {
            e.preventDefault();
            var $form = $('#SchannelAddChannelForm');
            $('#modalFormMsg').addClass('hide').html('');

            $('#modalFormMsg').html('').addClass('hide');
            $http({
                method  : 'POST',
                url     : $form.attr('action'),
                data    : $form.serialize(),
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data) {
                if (data.action == 'success') {
                    $modalInstance.close(data);
                } else {
                    $.each(data.errors, function(key, value){
                        $.each(value, function(k, m){
                            $('#modalFormMsg').append(m + '<br>');
                        });
                    });
                    $('#modalFormMsg').removeClass('hide');
                }
            });
            return false;
        };

        $scope.close = function (e) {
            e.preventDefault();
            $modalInstance.dismiss('cancel');
        };
    }

    $scope.partName = function($part_id) {
        return $scope.parts[$part_id];
    }

    $scope.partStatus = function($status) {
        if($status) {
            return 'Yes';
        } else {
            return 'No';
        }
    }
}]);