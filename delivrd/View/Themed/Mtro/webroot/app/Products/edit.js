var app = angular.module('delivrd-app',['ui.bootstrap', 'template/modal/backdrop.html', 'template/modal/window.html']);

angular.module("template/modal/backdrop.html", []).run(["$templateCache", function($templateCache) {
  $templateCache.put("template/modal/backdrop.html",
    "<div class=\"modal-backdrop fade\" ng-class=\"{in: animate}\" ng-style=\"{'z-index': 10040 + index*10}\" ng-click=\"close($event)\"></div>");
}]);

angular.module("template/modal/window.html", []).run(["$templateCache", function($templateCache) {
    $templateCache.put("template/modal/window.html",
    "<div class=\"modal fade {{ windowClass }}\" ng-class=\"{in: animate}\" style='display:block;z-index:10050'  ng-transclude></div>");
}]);

app.controller( 'EditProduct', ['$scope', '$http', '$modal', function ($scope, $http, $modal) {
    $scope.product = product;
    $scope.channel_prices = channel_prices;
    $scope.schannels = schannels;
    $scope.schannels_a = schannels_a;

    $scope.product_parts = product_parts;
    $scope.parts = parts;
    $scope.parts_a = parts_a;

    console.log($scope.product_parts);

    $scope.removeKit = function($id) {
        $http({
            method  : 'POST',
            url     : siteUrl+'products/delete_product_kit/',
            data    : 'id='+$id,
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function(data) {
            if (data.action == 'success') {
                angular.forEach($scope.product_parts, function(value, index){
                    if(value.Kit.id == $id) {
                        $scope.product_parts.splice(index,1);
                        $scope.product_parts.splice(index,0);
                    }
                });
                toastr["success"]('Part successfully deleted.');
            } else {
                toastr["success"]('Error, please try again.');
            }
        });
    }

    $scope.addKit = function() {

        if($scope.parts_id == undefined || $scope.parts_id == '') {
            toastr.error('Please select product.');
            return false;
        }
        if($scope.quantity == undefined) {
            toastr.error('Please enter quantity.');
            return false;
        }

        $http({
            method  : 'POST',
            url     : siteUrl+'products/add_product_kit/',
            data    : 'parts_id='+$scope.parts_id.id+'&product_id='+$scope.product.Product.id+'&quantity='+$scope.quantity+'&active='+$scope.active,
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function(data) {
            if (data.action == 'success') {
                var is_new = true;
                angular.forEach($scope.product_parts, function(value,index){
                    if(value.Kit.parts_id == data.part.parts_id) {
                        is_new = false;
                        $scope.product_parts[index].Kit.quantity = data.part.quantity;
                        $scope.product_parts[index].Kit.active = data.part.active;
                    }
                });

                if(is_new) {
                    var pr = { 'Kit': data.part };
                    $scope.product_parts.unshift(pr);
                }
                toastr["success"]('Part successfully added.');
            } else {
                toastr["error"]('Error, please enter valid data and try again.');
            }
        });

        $scope.parts_id = '';
        $scope.quantity = '';
    }

    $scope.removePrice = function($id) {
        $http({
            method  : 'POST',
            url     : siteUrl+'products/delete_schannel_price/',
            data    : 'id='+$id,
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function(data) {
            if (data.action == 'success') {
                angular.forEach($scope.channel_prices, function(value, index){
                    if(value.ProductsPrices.id == $id) {
                        $scope.channel_prices.splice(index,1);
                        $scope.channel_prices.splice(index,0);
                    }
                });
                toastr["success"]('Price successfully deleted.');
            } else {
                toastr["success"]('Error, please try again.');
            }
        });
    }

    $scope.addPrice = function() {

        if($scope.schannel_price == undefined || $scope.schannel_price <= 0) {
            toastr.error('Please enter valid Price.');
            return false;
        }
        if($scope.schannel_id == undefined) {
            toastr.error('Please select Channel.');
            return false;
        }

        $http({
            method  : 'POST',
            url     : siteUrl+'products/add_schannel_price/',
            data    : 'schannel_id='+$scope.schannel_id.id+'&product_id='+$scope.product.Product.id+'&value='+$scope.schannel_price,
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function(data) {
            if (data.action == 'success') {
                var is_new = true;
                angular.forEach($scope.channel_prices, function(value,index){
                    if(value.ProductsPrices.schannel_id == data.price.schannel_id) {
                        is_new = false;
                        $scope.channel_prices[index].ProductsPrices.value = data.price.value;
                    }
                });

                if(is_new) {
                    var pr = { 'ProductsPrices': data.price };
                    $scope.channel_prices.unshift(pr);
                }
                toastr["success"]('Price successfully added.');
            } else {
                toastr["error"]('Error, please enter valid data and try again.');
                /*$.each(data.errors, function(key, value){
                    if(key == 'value');
                    $.each(value, function(k, m){
                        //$('#modalFormMsg').append(m);
                    });
                });
                $('#modalFormMsg').removeClass('hide');*/
            }
        });
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

    $scope.addDocument = function(model_id, product_sku) {
        var modalDocumentInstance = $modal.open({
            templateUrl: siteUrl+'documents/view/product/'+model_id+'?f=l&bust='+Math.random().toString(36).slice(2),
            controller: 'modalDocumentInstanceCtrl',
            resolve: {
                model_id: function () {
                    return model_id;
                },
                model_type: function () {
                    return 'product';
                },
                model_name: function () {
                    return product_sku;
                }
            }
        });

        modalDocumentInstance.result.then(function (data) {
            
        });
    }
}]);