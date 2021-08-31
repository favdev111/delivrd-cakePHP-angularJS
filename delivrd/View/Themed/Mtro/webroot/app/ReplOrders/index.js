var app = angular.module('delivrd-app',['ui.bootstrap', 'ui.bootstrap.modal', 'template/modal/backdrop.html', 'template/modal/window.html']);
    
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

app.controller( 'ReplOrderList', ['$scope', '$http', '$modal', '$sce', function ($scope, $http, $modal, $sce) {

    $scope.addDocument = function(order_id) {
        var modalDocumentInstance = $modal.open({
            templateUrl: siteUrl+'documents/view/order/'+order_id+'?f=l&bust='+Math.random().toString(36).slice(2),
            controller: 'modalDocumentInstanceCtrl',
            resolve: {
                model_id: function () {
                    return order_id;
                },
                model_type: function () {
                    return 'order';
                },
                model_name: function () {
                    return order_id;
                }
            }
        });

        modalDocumentInstance.result.then(function (data) {
            
        });
    }




    $scope.makePaid = function(order_id) {
        var modalPaymentInstance = $modal.open({
            templateUrl: siteUrl+'replorders/paid/'+order_id+'?f=l&bust='+Math.random().toString(36).slice(2),
            controller: modalPaymentInstanceCtrl
        });

        modalPaymentInstance.result.then(function (data) {

        });
    }


    var modalPaymentInstanceCtrl = function($scope, $modalInstance, $http) {
        // Submit EditProduct Form
        $scope.submitPaymentStatus = function (e) {
            e.preventDefault();
            var $form = $('#OrderPaidForm');

            $('#modalFormMsg').html('').addClass('hide');
            $http({
                method  : 'POST',
                url     : $form.attr('action'),
                data    : $form.serialize(),
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data) {
                if (data.action == 'success') {
                    //$modalInstance.close(data.order);
                    toastr.success('Order status updated', "", {tapToDismiss: false, closeButton:true, closeHtml: '<button><i class="fa fa-close"></i></button>',timeOut: false,})
                    setTimeout(function(){ location.reload(); }, 1000);
                } else {
                    $.each(data.errors, function(key, value){
                        $.each(value, function(k, m){
                            $('#modalFormMsg').append(m);
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
    };


    $scope.cancelMultiple = function() {
        var checkedVals = $('.checkboxes:checkbox:checked').map(function() {
            return this.value;
        }).get();

        if(checkedVals == '') {
            toastr.error('Please select orders', "", {tapToDismiss: false, closeButton:true, closeHtml: '<button><i class="fa fa-close"></i></button>',timeOut: false,})
        } else {
            $http({
                method  : 'POST',
                url     : siteUrl+'orders/cancel_multiple',
                data    : 'ajax=1&data[Order][order_id]='+checkedVals.join(","),
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data) {
                if (data.action == 'success') {
                    //getData();
                    toastr["success"](data.message);
                } else if(data.action == 'warning') {
                    //getData();
                    toastr['warning'](data.message);
                } else {
                    //getData();
                    toastr['error'](data.message);
                }
                $('#OrderIndexForm').show();
                $('#multiFunctions').hide();
            });
            $("#selected_ids").val(checkedVals.join(","));
            $('#delete_form').submit();
        }
        return false;
    }
}]);

app.factory('HttpInterceptor', function($templateCache, APP_CONFIG) {
    return {
        'request': function(request) {
            if (request.method === 'GET' && $templateCache.get(request.url) === undefined) {
                request.url += '?ver=' + APP_CONFIG.VERSION;
            }
            return request;
        }
    };
});