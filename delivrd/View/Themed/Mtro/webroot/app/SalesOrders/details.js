var app = angular.module('delivrd-app',['ui.bootstrap.modal', "template/modal/backdrop.html","template/modal/window.html"]);

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

app.directive('tooltip', function(){
    return {
        restrict: 'A',
        link: function(scope, element, attrs){
            element.hover(function(){
                // on mouseenter
                element.tooltip('show');
            }, function(){
                // on mouseleave
                element.tooltip('hide');
            });
        }
    };
});

var SalesOrderDetails = function($scope, $http, $modal, $sce) {

    $scope.helpers = Delvrd.helpers;
    
    $scope.order = order;
    $scope.order_costs = order_costs;
    $scope.costs_types = costs_types;
    $scope.is_write = is_write;
    $scope.lines = lines;
    $scope.sortType = ['OrdersLine.line_number'];
    $scope.total = total;
    $scope.currency = currency;
    $scope.warehouse = warehouse;

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
        } else if($status_id == '50') {
            $status_text = "<span class='label label-default'>Canceled</span>";
        } else if($status_id == '55') {
            $status_text = "<span class='label label-success'>Paid</span>";
        } else if($status_id == '60') {
            $status_text = "<span class='label label-info'>In Wave</span>";
        } else {
            $status_text = "<span class='label label-warning'>Shipped</span>";
        }
        return $sce.trustAsHtml($status_text);
    }

    $scope.displayPrice = function($price) {
        if($price == '' || $price == 'null' || $price == null) {
            return '0.00';
        } else {
            return parseFloat($price).toFixed(2);
        }
    }

    $scope.countAmount = function($percentage) {
        var amount = $scope.total.linestotal * $percentage/100;
        return parseFloat(amount).toFixed(2);
    }

    $scope.addCostType = function($type) {
        return $scope.costs_types[$type];
    }

    // Additional Costs
    $scope.additionalCosts = function(order_id) {
        var modalAdditionalCostsInstance = $modal.open({
            templateUrl: siteUrl+'orderscosts/add_costs/'+order_id+'?bust='+Math.random().toString(36).slice(2),
            controller: ModalAdditionalCostsInstanceCtrl
        });

        modalAdditionalCostsInstance.result.then(function (data) {
            if(data.action == 'success') {
                $scope.order_costs.push(data.row);
                countGrandTotal();
                toastr["success"](data.message);
            } else {
                toastr["error"](data.message);
            }
        });
    }

    var ModalAdditionalCostsInstanceCtrl = function($scope, $modalInstance, $http) {

        $scope.addCosts = function (e) {
            e.preventDefault();
            var $form = $('#OrdersCostsAddCostsForm');

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

    $scope.makePaid = function(order_id) {
        var modalPaymentInstance = $modal.open({
            templateUrl: siteUrl+'salesorders/paid/'+order_id+'?f=l&bust='+Math.random().toString(36).slice(2),
            controller: modalPaymentInstanceCtrl
        });

        modalPaymentInstance.result.then(function (order) {
            $scope.order = order;
            toastr.success('Order status updated', "", {tapToDismiss: false, closeButton:true, closeHtml: '<button><i class="fa fa-close"></i></button>',timeOut: false,})
        });

        return false;
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

    $scope.deleteAddCost = function(line_id) {
        $http({
            method  : 'POST',
            url     : siteUrl+'orderscosts/delete/'+line_id,
            data    : {id: line_id},
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function(data) {
            if (data.action == 'success') {
                angular.forEach($scope.order_costs, function(value,index){
                    if(value.OrdersCosts.id == line_id) {
                        $scope.order_costs.splice(index,1);
                        $scope.order_costs.splice(index,0);
                    }
                });
                countGrandTotal();
                toastr["success"](data.message);
            } else {
                toastr["error"](data.message);
            }
        });
        return false;
    }

    // End Additional Costs

    // Issue all lines
    $scope.issueAllLines = function(order_id, is_complete) {
        if(is_complete) {
            var url = siteUrl+'orders_lines/issuealllines/'+order_id+'?1=1';
        } else {
            var url = siteUrl+'orders_lines/issuealllines/'+order_id+'?status_id=3';
        }
        $http({
            method  : 'POST',
            url     : url,
            data    : 'order_id='+order_id,
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function(data) {
            if (data.action == 'success') {
                toastr['success'](data.msg);
                $scope.order.Order.status_id = data.status_id;
                // change issue values
                $.each($scope.lines, function(key, val) {
                    $scope.lines[key].OrdersLine.sentqty = val.OrdersLine.quantity;
                });
            } else if (data.action == 'confirm') {
                var ModalIssueInstance = $modal.open({
                    templateUrl: siteUrl+'orders_lines/issueallconfirm/'+order_id+'?is_complete='+is_complete+'&bust='+Math.random().toString(36).slice(2),
                    controller: ModalIssueInstanceCtrl
                });

                ModalIssueInstance.result.then(function (data) {
                    if(data.action == 'success') {
                        $scope.order.Order.status_id = data.status_id;
                        // change issue values
                        $.each($scope.lines, function(key, val) {
                            $scope.lines[key].OrdersLine.sentqty = val.OrdersLine.quantity;
                        });
                        toastr["success"](data.msg);
                    } else {
                        toastr["error"](data.msg);
                    }
                });
            } else {
                toastr['error'](data.msg);
            }
        });
    }

    var ModalIssueInstanceCtrl = function($scope, $modalInstance, $http, $rootScope) {
        $scope.issueAllConfirm = function(order_id, is_complete) {
            if(is_complete == 1) {
                var url = siteUrl+'orders_lines/issuealllines/'+order_id+'?confirm=1';
            } else {
                var url = siteUrl+'orders_lines/issuealllines/'+order_id+'?status_id=3&confirm=1';
            }
            $http({
                method  : 'POST',
                url     : url,
                data    : 'order_id='+order_id,
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data) {
                if (data.action == 'success') {
                    $modalInstance.close(data);
                } else {
                    $modalInstance.close(data);
                }
            })
        };

        $scope.close = function (e) {
            e.preventDefault();
            $modalInstance.dismiss('cancel');
        };
    }
    // End issue all lines

    $scope.chStatus = function() {
        $scope.order.Order.status_id = 14;
        toastr["success"]('Success');
    }

    $scope.toDraftOrder = function(order_id) {
        var modalToDraftInstance = $modal.open({
            templateUrl: siteUrl+'orders/todraft/'+order_id+'?f=l&bust='+Math.random().toString(36).slice(2),
            controller: ModalToDraftInstanceCtrl
        });

        modalToDraftInstance.result.then(function (data) {
            if(data.action == 'success') {
                $scope.order.Order.status_id = 14;
                toastr["success"](data.message);
            } else {
                toastr["error"](data.message);
            }
        });
    }

    var ModalToDraftInstanceCtrl = function($scope, $modalInstance, $http) {
        $scope.complete = function(order_id) {
            if($('#confirmReturn').attr('checked') == 'checked') {
                var return_issue = 1;
            } else {
                var return_issue = 0;
            }
            $http({
                method  : 'POST',
                url     : siteUrl+'orders/todraft/'+order_id,
                data    : 'order_id='+order_id+'&return_issue='+return_issue,
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


    $scope.open = function(lineid) {
        if(lineid == '999999') {
            var url = siteUrl+'salesorders/add_line/'+orderId+'/'+lineid+'?bust='+Math.random().toString(36).slice(2);
        } else {
            var url = siteUrl+'salesorders/add_line/'+orderId+'?bust='+Math.random().toString(36).slice(2);
        }
        var modalInstance = $modal.open({
            templateUrl: url,
            controller: ModalInstanceCtrl,
            scope: $scope
        });

        modalInstance.result.then(function (row) {
            $scope.lines.push(row);
            $scope.total.linestotal = parseFloat($scope.total.linestotal).toFixed(2) + parseFloat(row.OrdersLine.total_line).toFixed(2);
            //$scope.total.grand = parseInt($scope.total.grand) + parseInt(row.OrdersLine.total_line);
            countGrandTotal();
        });
    }

    $scope.$on("modalAddLine", function(event, data) {
        $scope.lines.push(data.row);
        $scope.total.linestotal = parseFloat($scope.total.linestotal) + parseFloat(data.row.OrdersLine.total_line);
        //$scope.total.grand = parseInt($scope.total.grand) + parseInt(data.row.OrdersLine.total_line);
        countGrandTotal();
    });

    $scope.openEditRow = function(item) {
        //var id = item.attributes['data-id'].value;
        var modalEditInstance = $modal.open({
            templateUrl: siteUrl+"salesorders/edit_line/"+item+'?bust='+Math.random().toString(36).slice(2),
            controller: ModalEditInstanceCtrl
            
        });

        modalEditInstance.result.then(function (data) {
            angular.forEach($scope.lines, function(value,index) {
                if(value.OrdersLine.id == data.row.OrdersLine.id) {
                    $scope.lines.splice(index,1);
                    $scope.lines.splice(index,0,data.row);
                }
            });
            $scope.total = data.ordertotals;
            //countGrandTotal();
        });
    }

    $scope.openIssue = function(item) {
        var modalSendInstance = $modal.open({
            templateUrl: siteUrl+"salesorders/send_line/"+item+'?bust='+Math.random().toString(36).slice(2),
            controller: ModalSendInstanceCtrl
            
        });

        modalSendInstance.result.then(function (line) {
            angular.forEach($scope.lines, function(value,index){
                if(value.OrdersLine.id == line.OrdersLine.id) {
                    $scope.lines.splice(index,1);
                    $scope.lines.splice(index,0,line);
                    $scope.order.Order.status_id = 3;
                }
            });
        });
    }

    $scope.editShipping = function() {
        var modalShippingInstance = $modal.open({
            templateUrl: siteUrl+'salesorders/edit_shipping/'+orderId+'?bust='+Math.random().toString(36).slice(2),
            controller: ModalShippingInstanceCtrl
            
        });

        modalShippingInstance.result.then(function (order) {
            $scope.order = order;
            //$scope.total.shipping = parseFloat(order.Order.shipping_costs);
            //countGrandTotal();
        });
    }

    $scope.editDetails = function() {
        var modalDetailsInstance = $modal.open({
            templateUrl: siteUrl+'salesorders/edit_details/'+orderId+'?bust='+Math.random().toString(36).slice(2),
            controller: ModalDetailsInstanceCtrl
            
        });

        modalDetailsInstance.result.then(function (order) {
            $scope.order = order;
            if(order.Order.shipping_costs == null || order.Order.shipping_costs == 'null' || order.Order.shipping_costs == '') {
                $scope.total.shipping = 0;
            } else {
                $scope.total.shipping = parseFloat(order.Order.shipping_costs).toFixed(2);
            }
            
            $scope.total.grand = parseFloat($scope.total.linestotal).toFixed(2) + parseFloat(order.Order.shipping_costs).toFixed(2);
            countGrandTotal();
        });
    }


    $scope.removeLine = function(line_id) {
        $('#modalFormMsg').html('').addClass('hide');
        $http({
            method  : 'POST',
            url     : siteUrl+'salesorders/delete_line/'+line_id,
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
                //countGrandTotal();
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
};

var ModalInstanceCtrl = function($scope, $modalInstance, $http, $rootScope) {

    $scope.order = order;
    $scope.lines = [];
    $scope.sortType = ['OrdersLine.line_number'];
    //$scope.total = total;
    $scope.currency = currency;
    $scope.warehouse = warehouse;

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
	                $rootScope.$broadcast("modalAddLine", {row: data.row});
	                $scope.lines.push(data.row);

                    setTimeout(function() { $modalInstance.close(data.row); }, 500);
                } else {
                    $rootScope.$broadcast("modalAddLine", {row: data.row});
                    $scope.lines.push(data.row);
                    
                    $('#OrdersLineQuantity').val('');
                    $('#OrdersLineUnitPrice').val('');
                    $('#OrdersLineComments').val('');

                    $('#warehouse_id').val('').trigger('change');
                    $('#product_id').val('').trigger('change');
                    $('.select2-chosen').html('<div>Select...</div>');
                }
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

        $('#modalConfirmMsg').addClass('hide');
        $('#modalFormMsg').html('').addClass('hide');

        $http({
            method  : 'POST',
            url     : $form.attr('action'),
            data    : $form.serialize(),
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function(data) {
            if (data.action == 'success') {
                $modalInstance.close(data.orderline);
            } else if (data.action == 'confirm') {
                $('#modalConfirmMsg').removeClass('hide');
                $btn.button('reset');
            } else {
                $.each(data.errors, function(key, value){
                    $btn.button('reset');
                    $.each(value, function(k, m){
                        $('#modalFormMsg').append(m);
                    });
                });
                $btn.button('reset');
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