var app = angular.module('delivrd-app',['ui.bootstrap', "template/modal/backdrop.html","template/modal/window.html"]);

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

var SalesOrderDetails = function($scope, $http, $modal, $sce) {

    $scope.helpers = Delvrd.helpers;
    
    $scope.order = order;
    $scope.is_write = is_write;
    $scope.is_shipment = is_shipment;
    $scope.lines = lines;
    $scope.total = total;
    $scope.currency = currency;
    $scope.warehouse = warehouse;

    $scope.is_blanket = 0;

    $scope.sortType = ['OrdersLine.line_number'];

	function countGrandTotal() {
		var total_new = parseFloat($scope.total.linestotal);
		$.each($scope.order_costs, function(key, val) {
			var sum = parseFloat(val.OrdersCosts.amount);
			if(val.OrdersCosts.uom == 'percentage') {
				sum = $scope.total.linestotal * (val.OrdersCosts.amount) / 100;
			}
			if(val.OrdersCosts.type == 'discount') {
				total_new = total_new - sum;
			} else {
				total_new = total_new + sum;
			}
		});
		$scope.total.grand_new = (total_new + parseFloat($scope.total.shipping)).toFixed(2);
	}

    $scope.status = function($status_id) {
        var $status_text;
        if($status_id == '14') {
            $status_text = "<span class='label label-default'>Draft</span>";
        } else if($status_id == '2') {
            $status_text = "<span class='label label-info'>Released</span>";
        } else if($status_id == '3') {
            $status_text = "<span class='label bg-yellow'>Shipping Processing</span>";
        } else if($status_id == '8') {
            $status_text = "<span class='label label-success'>Shipped</span>";
        } else if($status_id == '4') {
            $status_text = "<span class='label label-success'>Completed</span>";
        } else if($status_id == '55') {
            $status_text = "<span class='label label-success'>Paid</span>";
        } else {
            $status_text = "<span class='label label-warning'>Shipped</span>";
        }
        return $sce.trustAsHtml($status_text); // $sce is not defined
    }

    $scope.displayPrice = function($price) {
        if($price == '' || $price == 'null' || $price == null) {
            return '0.00';
        } else {
            return parseFloat($price).toFixed(2);
        }
    }

    $scope.completeOrder = function(order_id) {
        var modalCompleteInstance = $modal.open({
            templateUrl: siteUrl+'orders/complete/'+order_id+'?f=d&bust='+Math.random().toString(36).slice(2),
            controller: ModalCompleteInstanceCtrl
        });

        modalCompleteInstance.result.then(function (data) {
            if(data.action == 'success') {
                $scope.order.Order.status_id = 4;
                toastr["success"](data.message);
            } else {
                toastr["error"](data.message);
            }
        });
    }

    var ModalCompleteInstanceCtrl = function($scope, $modalInstance, $http) {

        $scope.complete = function(order_id) {
            $http({
                method  : 'POST',
                url     : siteUrl+'orders/complete/'+order_id,
                data    : 'order_id='+order_id,
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data) {
                if (data.action == 'success') {
                    $modalInstance.close(data);
                } else {
                    toastr['error'](data.message);
                }
            });
        };

        $scope.close = function (e) {
            e.preventDefault();
            $modalInstance.dismiss('cancel');
        };
    }

    $scope.addLine = function() {
        var url = siteUrl+'replorders/add_line/'+orderId+'/?bust='+Math.random().toString(36).slice(2);
        
        var addLineInstance = $modal.open({
            templateUrl: url,
            controller: AddLineInstanceCtrl
        });

        addLineInstance.result.then(function (row) {
            $scope.lines.push(row);
            $scope.total.linestotal = parseFloat($scope.total.linestotal).toFixed(2) + parseFloat(row.OrdersLine.total_line).toFixed(2);
            $scope.total.grand = parseFloat($scope.total.grand).toFixed(2) + parseFloat(row.OrdersLine.total_line).toFixed(2);
        });
    }

    $scope.addBlanketLine = function() {
        var url = siteUrl+'replorders/add_blanket_line/'+orderId+'/?bust='+Math.random().toString(36).slice(2);
        
        var addLineInstance = $modal.open({
            templateUrl: url,
            controller: AddLineInstanceCtrl
        });

        addLineInstance.result.then(function (row) {
            $scope.lines.push(row);
            $scope.total.linestotal = parseFloat($scope.total.linestotal).toFixed(2) + parseFloat(row.OrdersLine.total_line).toFixed(2);
            $scope.total.grand = parseFloat($scope.total.grand).toFixed(2) + parseFloat(row.OrdersLine.total_line).toFixed(2);
        });
    }

    $scope.$on("modalAddLine", function(event, data) {
        $scope.lines.push(data.row);
	    $scope.total.linestotal = parseFloat($scope.total.linestotal) + parseFloat(data.row.OrdersLine.total_line);
        //$scope.total.linestotal = parseFloat($scope.total.linestotal).toFixed(2) + parseFloat(data.row.OrdersLine.total_line).toFixed(2);
        // $scope.total.grand = parseFloat($scope.total.grand).toFixed(2) + parseFloat(data.row.OrdersLine.total_line).toFixed(2);
	    countGrandTotal();
        console.log($scope.total.linestotal, $scope.total.grand);
    });

    $scope.openEditRow = function(item) {
        //var id = item.attributes['data-id'].value;
        var modalEditInstance = $modal.open({
            templateUrl: siteUrl+"replorders/edit_line/"+item+'?bust='+Math.random().toString(36).slice(2),
            controller: ModalEditInstanceCtrl
            
        });

        modalEditInstance.result.then(function (data) {
            angular.forEach($scope.lines, function(value,index){
                if(value.OrdersLine.id == data.row.OrdersLine.id) {
                    $scope.lines.splice(index,1);
                    $scope.lines.splice(index,0,data.row);
                }
            });
            $scope.total = data.ordertotals;
        });
    }

    $scope.openIssue = function(item) {
        var modalSendInstance = $modal.open({
            templateUrl: siteUrl+"replorders/send_line/"+item+'?bust='+Math.random().toString(36).slice(2),
            controller: ModalSendInstanceCtrl
            
        });

        modalSendInstance.result.then(function (line) {
            angular.forEach($scope.lines, function(value,index){
                if(value.OrdersLine.id == line.OrdersLine.id) {
                    $scope.lines.splice(index,1);
                    $scope.lines.splice(index,0,line);
                }
            });
        });
    }

    $scope.editShipping = function() {
        var modalShippingInstance = $modal.open({
            templateUrl: siteUrl+'replorders/edit_shipping/'+orderId+'?bust='+Math.random().toString(36).slice(2),
            controller: ModalShippingInstanceCtrl
            
        });

        modalShippingInstance.result.then(function (order) {
            $scope.order = order;
        });
    }

    $scope.editDetails = function() {
        var modalDetailsInstance = $modal.open({
            templateUrl: siteUrl+'replorders/edit_details/'+orderId+'?bust='+Math.random().toString(36).slice(2),
            controller: ModalDetailsInstanceCtrl
            
        });

        modalDetailsInstance.result.then(function (order) {
            $scope.order = order;
            $scope.total.shipping = order.Order.shipping_costs;
            $scope.total.grand = parseFloat($scope.total.linestotal).toFixed(2) + parseFloat(order.Order.shipping_costs).toFixed(2);
        });
    }


    $scope.removeLine = function(line_id) {
        $('#modalFormMsg').html('').addClass('hide');
        $http({
            method  : 'POST',
            url     : siteUrl+'replorders/delete_line/'+line_id,
            data    : {line_id: line_id},
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function(data) {
            if (data.action == 'success') {
                angular.forEach($scope.lines, function(value,index){
                    if(value.OrdersLine.id == line_id) {
                        $scope.lines.splice(index,1);
                        $scope.lines.splice(index,0);
                    }
                });
                $scope.total = data.ordertotals;
            } else {
                $('#modalFormMsg').append('Can\'t remove this row now. Please try againe.');
                $('#modalFormMsg').removeClass('hide');
            }
        });
        return false;
    }

    $scope.formatDate = function(date){
        var dateOut = new Date(date);
        return dateOut;
    };

    $scope.is_writeable = function(warehouse_id) {
        if($scope.warehouse[warehouse_id] == 'w' || $scope.warehouse[warehouse_id] == 'rw') {
            return true;
        } else {
            return false;
        }
    }

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

        modalPaymentInstance.result.then(function (order) {
            $scope.order = order;
            toastr.success('Order status updated', "", {tapToDismiss: false, closeButton:true, closeHtml: '<button><i class="fa fa-close"></i></button>',timeOut: false,})
        });

        return false;
    }
};

var AddLineInstanceCtrl = function($scope, $modalInstance, $http, $rootScope) {

    $scope.order = order;

    $scope.lines = [];
    $scope.sortType = ['OrdersLine.line_number'];

    $scope.total = total;
    $scope.currency = currency;
    $scope.warehouse = warehouse;
    $scope.is_blanket = 0;

    var typeSubmit = 'close';
    // Submit AddProduct Form
    $scope.addProduct = function (e) {
        e.preventDefault();
        var $form = $('#OrdersLineAddLineForm');
        $('#modalFormMsg').html('').addClass('hide');
        $http({
            method  : 'POST',
            url     : $form.attr('action'),
            data    : $form.serialize(),
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function(data) {
            if (data.action == 'success') {
                if(typeSubmit == 'close') {
                    $modalInstance.close(data.row);
                } else {
                    $rootScope.$broadcast("modalAddLine", {row: data.row});
                    $scope.lines.push(data.row);
                    //$scope.total.linestotal = parseFloat($scope.total.linestotal).toFixed(2) + parseFloat(data.row.OrdersLine.total_line).toFixed(2);
                    //$scope.total.grand = parseFloat($scope.total.grand).toFixed(2) + parseFloat(data.row.OrdersLine.total_line).toFixed(2);

                    $('#OrdersLineQuantity').val('');
                    $('#OrdersLineUnitPrice').val('');
                    $('#OrdersLineComments').val('');
                    $('#OrderScheduleDeliveryDate').val('');
                }
            } else {
                $.each(data.errors, function(key, value){
                    $.each(value, function(k, m){
                        $('#modalFormMsg').append(m);
                    });
                });
                $('#modalFormMsg').append(data.message);
                $('#modalFormMsg').removeClass('hide');
            }
        });
        return false;
    };

    $scope.addProductClose = function(event) {
        typeSubmit = 'close';
    }

    $scope.addProductContinue = function(event) {
        typeSubmit = 'continue';
    }

    $scope.close = function (e) {
        e.preventDefault();
        $modalInstance.dismiss('cancel');
    };
};

var ModalEditInstanceCtrl = function($scope, $modalInstance, $http) {
    // Submit EditProduct Form
    $scope.editProduct = function (e) {
        e.preventDefault();
        var $form = $('#OrdersLineEditLineForm');

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
                        $('#modalFormMsg').append(m);
                    });
                });
                $('#modalFormMsg').append(data.message);
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

var ModalSendInstanceCtrl = function($scope, $modalInstance, $http) {
    // Submit sendProduct Form
    $scope.sendProduct = function (e) {
        e.preventDefault();
        var $form = $('#OrdersLineSendLineForm');
        var $btn = $('#issueLineBtn').button('loading');

        $('#modalFormMsg').html('').addClass('hide');
        $http({
            method  : 'POST',
            url     : $form.attr('action'),
            data    : $form.serialize(),
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function(data) {
            if (data.status == true) {
                $modalInstance.close(data.orderline);
            } else {
                $btn.button('reset');
                $.each(data.errors, function(key, value){
                    $.each(value, function(k, m){
                        $('#modalFormMsg').append(m);
                    });
                });
                $('#modalFormMsg').append(data.message);
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

var ModalDetailsInstanceCtrl = function($scope, $modalInstance, $http) {
    // Submit EditProduct Form
    $scope.editDetails = function (e) {
        e.preventDefault();
        var $form = $('#OrderEditDetailsForm');

        $('#modalFormMsg').html('').addClass('hide');
        $http({
            method  : 'POST',
            url     : $form.attr('action'),
            data    : $form.serialize(),
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function(data) {
            if (data.action == 'success') {
                $modalInstance.close(data.order);
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

var ModalShippingInstanceCtrl = function($scope, $modalInstance, $http) {
    // Submit EditProduct Form
    $scope.editShipping = function (e) {
        e.preventDefault();
        var $form = $('#OrderEditShippingForm');

        $('#modalFormMsg').html('').addClass('hide');
        $http({
            method  : 'POST',
            url     : $form.attr('action'),
            data    : $form.serialize(),
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function(data) {
            if (data.action == 'success') {
                $modalInstance.close(data.order);
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
                $modalInstance.close(data.order);
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

app.directive('ngConfirmClick', [
    function(){
        return {
            link: function (scope, element, attr) {
                var msg = attr.ngConfirmClick || "Are you sure?";
                var clickAction = attr.confirmedClick;
                element.bind('click',function (event) {
                    if ( window.confirm(msg) ) {
                        scope.$eval(clickAction)
                    }
                });
            }
        };
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