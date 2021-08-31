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

var IssueProducts = function($scope, $http, $modal, $sce) {
    $scope.order = order;

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
            } else if (data.action == 'confirm') {
                console.log('Confirm');
                var ModalIssueInstance = $modal.open({
                    templateUrl: siteUrl+'orders_lines/issueallconfirm/'+order_id+'?is_complete='+is_complete+'&bust='+Math.random().toString(36).slice(2),
                    controller: ModalIssueInstanceCtrl
                });

                ModalIssueInstance.result.then(function (data) {
                    if(data.action == 'success') {
                        $scope.order.Order.status_id = data.status_id;
                        toastr["success"](data.msg);
                        if(data.status_id == 4) {
                            window.location = siteUrl+'salesorders/details/'+orderId;
                        } else {
                            $('#flasMsg').removeClass('hidden');
                            $('.lineStatus').removeClass('danger').addClass('success');
                            $('.lineRow').each(function(){
                                $(this).find('#OrdersLineSentqty').val($(this).find('#OrdersLineQuantity').val());
                            });
                        }
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
}

$(document).ready(function(){
    if(show_img_column) {
        $('.img_column').removeClass('hide');
    }
    var diff = 0;
    $('.receive_order').submit(function(){
        var $form = $(this);
        var formData = $form.serialize();
        $.ajax({
            method: 'POST',
            url: siteUrl + "orders_lines/issuelines/",
            data: formData,
            datatype:'json',
        }).success(function (data) {
            var response = jQuery.parseJSON(data);
            if(response.action == 'confirm') {
                $('#issueOffset').html(response.issue);
                $('#warehouseName').html(response.warehouse);
                $('#invQty').html(response.inventory_qty);
                $('#allInvQty').html('<h4>Other locations</h4>');
                $.each(response.all_inventory_qty, function(k, v) {
                    if(response.warehouse != v.Warehouse.name) {
                        $('#allInvQty').append('<p>Quantity in location <span>'+v.Warehouse.name+'</span>: <b>'+v.Inventory.quantity+'</b></p>')
                    }
                });
                
                $('#modalLineId').val(response.line_id);
                confirm_negative($form.find('input.lineId').val(), $form.parents('tr').find('#OrdersLineWarehouseId').val(), $form.parents('tr').find('#OrdersLineSentqty').val());
            } else {
                if(response.action == 'danger') {
                    response.action = 'error';
                }

                
                toastr[response.action](response.message);
                var sentqty = $form.parents('tr').find('#OrdersLineSentqty').val()
                var quantity = $form.parents('tr').find('#OrdersLineQuantity').val()
                if(sentqty < quantity) {
                    $form.parents('tr').find('.lineStatus').removeClass('success').addClass('danger');
                } else {
                    $form.parents('tr').find('.lineStatus').removeClass('danger').addClass('success');
                }

                if(response.action == 'error' && diff > 0) {
                    $form.parents('tr').find('#OrdersLineSentqty').val(sentqty - diff);
                    diff = 0;
                }

                $('#autocomplete').val('').focus();

                checkAllStatus();
            }
        });
        return false;
    });

    function confirm_negative($lineId, warehouse_id, quantity) {
        $('#confirmModal').modal('show');
    }

    $('#confirmNegative').click(function() {
        var is_negative_alowed = 0;
        if($(this).attr('checked') == 'checked') {
            is_negative_alowed = 1
        }

        $.ajax({
            type: 'POST',
            url: siteUrl + 'salesorders/confirmNegative',
            data: 'negative_alowed='+is_negative_alowed,
            dataType:'json',
            beforeSend: function() {
                
            },
            success:function (r, status) {
                
            }
        });
    });
    $('#submitButton').click(function() {
        var line_id = $('#modalLineId').val();
        $('#line_'+line_id).find('#OrdersLineConfirm').val(1);
        $('#line_'+line_id).find('form').submit();
        $('#confirmModal').modal('hide');
        return false;
    });

    $('#autocomplete').scannerDetection({
        timeBeforeScanTest: 200, // wait for the next character for upto 200ms
        startChar: [120], // Prefix character for the cabled scanner (OPL6845R)
        endChar: [13], // be sure the scan is complete if key 13 (enter) is detected
        avgTimeByChar: 40, // it's not a barcode if a character takes longer than 40ms
        minLength: 6,
        onComplete: function(barcode, qty){
            $('#autocomplete').val(barcode);

            $('#line_'+order_list[barcode]).addClass('bg-green-turquoise');
            setTimeout(function() {
                $('#line_'+order_list[barcode]).removeClass('bg-green-turquoise');
            }, 300);

            if(order_list[barcode] == undefined) {
                $('#scannerError')[0].play();
                toastr['error']('The orders line not found. Please, try again.');
            } else {
                var quantity = $('#line_'+order_list[barcode]).find('#OrdersLineQuantity').val();
                var sentqty = $('#line_'+order_list[barcode]).find('#OrdersLineSentqty').val();
                sentqty = Number(sentqty);
                quantity = Number(quantity);

                if(sentqty < quantity) {
                    $('#scannerSuccess')[0].play();
                    $('#line_'+order_list[barcode]).find('#OrdersLineSentqty').val(sentqty + 1);
                    diff = 1;
                    $('#line_'+order_list[barcode]).find('form').submit();
                } else {
                    $('#scannerError')[0].play();
                    $('#autocomplete').val('').focus();
                    toastr['error']('The orders line could not be saved. Please, try again.');
                }
            }
            checkAllStatus();
        },
        /*onReceive: function(barcode, qty){
            $('#autocomplete').val(barcode);
            $('#scannerError')[0].play();
        },*/
        onError: function(barcode, qty){
            //$('#autocomplete').val(barcode);
            //$('#scannerError')[0].play();
        }
    });

    $('.skuCode').click(function() {
        var sku = $(this).data('sku');
        $('#autocomplete').scannerDetection(sku.toString());
        return false;
    });

    function checkAllStatus() {
        $.ajax({
            type: 'POST',
            url: siteUrl + 'orders/checkLineStatuses/'+ orderId,
            data: '',
            dataType:'json',
            success:function (r, status) {
                if(r.status == 'ready') {
                    $('#flasMsg').removeClass('hidden');
                } else {
                    $('#flasMsg').addClass('hidden');
                }
            }
        });
        
    }

    checkAllStatus();
});