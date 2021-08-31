var refreshSelect = function() {
    if (!element.select2Initialized) return;
    $timeout(function() {
        element.trigger('change');
    });
};

var app = angular.module('delivrd-app',['ui.bootstrap', 'ui.bootstrap.modal', 'template/modal/backdrop.html', 'template/modal/window.html'])
        .directive('customAutofocus', customAutofocus);
    
    app.directive('fileModel', ['$parse', function ($parse) {
        return {
           restrict: 'A',
           link: function(scope, element, attrs) {
              var model = $parse(attrs.fileModel);
              var modelSetter = model.assign;
              
              element.bind('change', function() {
                 scope.$apply(function() {
                    modelSetter(scope, element[0].files[0]);
                 });
              });
           }
        };
     }]);

    app.service('fileUpload', ['$http', function ($http) {
        this.uploadFileToUrl = function(file, uploadUrl){
            var fd = new FormData();
            fd.append('file', file);
            $http.post(uploadUrl, fd, {
                transformRequest: angular.identity,
                headers: {'Content-Type': undefined}
            })
            .success(function(){
            })
            .error(function(){
            });
        }
    }]);

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

    function customAutofocus($timeout) {
        //console.log($timeout);
        return {
            restrict: 'A',
            link: function(_scope, _element) {
                $timeout(function(){
                    _element[0].focus();
                }, 0);
            }
        }
    }

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

    app.controller( 'SalesOrderList', ['$scope', '$http', '$modal', '$sce', function ($scope, $http, $modal, $sce) {
        $scope.popup = popup;
        $scope.is_write = is_write;
        $scope.networks = networks;
        $scope.ship_networks = ship_networks;

        $scope.status_id = status_id;
        $scope.schannel_id = schannel_id;
        $scope.searchby = searchby;

        $scope.currentPage = 1;
        $scope.limit = limit;

        $scope.sortBy = 'Order.modified';
        $scope.sortDir = 'DESC';

        $scope.showall = 0;

        $scope.orders = [];
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
            var url = siteUrl + 'salesorders/ajax_index';
            url = url +'/page:'+$scope.currentPage;
            url = url +'?limit='+$scope.limit;
            if(productId) {
                url = url +'&product_id='+productId;
            }

            if($scope.schannel_id != '') {
                url = url +'&schannel_id='+$scope.schannel_id;
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
            if($scope.searchby != '') {
                url = url +'&searchby='+$scope.searchby;
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

                angular.forEach(response.data.rows, function(value,index){
                    response.data.rows[index].expanded = 'no';
                });

                angular.copy(response.data.rows, $scope.orders);
            }, function(response) {
                if(response.status == 403) {
                    window.location.href = siteUrl+'login';
                }
            });
        };

        $scope.makePaid = function(order_id) {
            var modalPaymentInstance = $modal.open({
                templateUrl: siteUrl+'salesorders/paid/'+order_id+'?f=l&bust='+Math.random().toString(36).slice(2),
                controller: modalPaymentInstanceCtrl
            });

            modalPaymentInstance.result.then(function (data) {
                angular.forEach($scope.orders, function(value,index){
                    if(value.Order.id == data.Order.id) {
                        $scope.orders[index] = data;
                        //$scope.documents.splice(index,1);
                        //$scope.documents.splice(index,0);
                    }
                });
            });
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
                        toastr.success('Order status updated', "", {tapToDismiss: false, closeButton:true, closeHtml: '<button><i class="fa fa-close"></i></button>',timeOut: false,})
                        //setTimeout(function(){ location.reload(); }, 1000);
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

        $scope.pageChanged = function(page) {
            getData();
        };
        
        var self = this;

        $scope.applySearch = function() {
            $scope.currentPage = 1;
            //$('select.limit').trigger('change');
            //console.log('applySearch');
            getData();
            return false;
        }

        $scope.showAll = function() {
            $scope.status_id = '';
            //$('OrderStatusId').val('');
            //$('OrderStatusId').trigger('change');
            $scope.schannel_id = '';
            $scope.searchby = '';
            $scope.currentPage = 1;
            $scope.showall = 1;
            $scope.limit = limit;

            $scope.sortBy = 'Order.modified';
            $scope.sortDir = 'DESC';
            //$('#OrderStatusId').trigger('change');
            getData();
            return false;
        }
        $scope.showAllCanceled = function() {
            $scope.status_id = 50;
            $scope.schannel_id = '';
            $scope.searchby = '';
            $scope.currentPage = 1;
            $scope.limit = 10;
            //$('#OrderStatusId').trigger('change');
            getData();
            return false;
        }

        $scope.is_order_write = function(order) {
            if(userUid == order.Order.user_id) {
                return true;
            }

            if(/w/.test($scope.networks[order.Order.user_id].access)) {
                return true;
            }
            return false;
        }
        
        $scope.is_shipment_write = function(order) {
            if(userUid == order.Order.user_id) {
                return true;
            }

            /* We need check access with count of wharehouse
            if(/w/.test($scope.ship_networks[order.Order.user_id].access)) {
                return true;
            }*/
            return false;
        }

        $scope.is_order_owner = function(order) {
            if(userUid == order.Order.user_id) {
                return true;
            }
            return false;
        }

        $scope.expendLines = function(order_id) {
            var is_expended = false;
            angular.forEach($scope.orders, function(value,index){
                if(value.Order.id == order_id) {
                    if($scope.orders[index].expanded == 'yes') {
                        is_expended = true;
                        $scope.orders[index].OrderLines = false;
                        $scope.orders[index].expanded = 'no';
                    }
                }
            });

            if(!is_expended) {
                $http({
                    method  : 'GET',
                    url     : siteUrl+'/salesorders/lines/'+order_id,
                    data    : '',
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
                }).success(function(data) {
                    if (data.action == 'success') {
                        angular.forEach($scope.orders, function(value,index){
                            if(value.Order.id == order_id) {
                                $scope.orders[index].OrderLines = data.OrderLines;
                                $scope.orders[index].expanded = 'yes';
                            }
                        });
                    }
                });
            }
            return false;
        }

        $scope.displayPrice = function($price) {
            if($price == '' || $price == 'null' || $price == null) {
                return '0.00';
            } else {
                return parseFloat($price).toFixed(2);
            }
        }

        $scope.lineStatus = function(orderline) {
            var req_qty = (orderline.OrdersLine.quantity - orderline.OrdersLine.sentqty);
            
            if(orderline[0].inv_totals >= req_qty) {
                return $sce.trustAsHtml('<i class="fa fa-square text-success" aria-hidden="true"></i>');
            } else {
                if(orderline[0].totals < req_qty) {
                    return $sce.trustAsHtml('<i class="fa fa-square text-danger" aria-hidden="true" title="No inventory exists"></i>');
                } else {
                    return $sce.trustAsHtml('<i class="fa fa-square font-yellow-saffron" aria-hidden="true" title="Not enough inventory in order inventory location"></i>');
                }
            }
        }

        $scope.deleteOrder = function(order_id) {
            if(confirm('Are you sure you want to delete order # '+order_id+'?')) {
                $http({
                    method  : 'POST',
                    url     : siteUrl+'orders/delete/'+order_id,
                    data    : 'order_id='+order_id,
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
                }).success(function(data) {
                    if (data.action == 'success') {
                        //self.tableParams.reload();
                        getData();
                        toastr["success"](data.message); 
                    } else {
                        toastr['error'](data.message);
                    }

                    $('#OrderIndexForm').show();
                    $('#multiFunctions').hide();
                });
            } else {
                return false;
            }
        };

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

        /*var modalDocumentInstanceCtrl = function($scope, $modalInstance, $http, fileUpload, model_id, model_type) {

            $scope.title = 'Sales Order #'+model_id;

            $scope.sortBy = 'Document.created';
            $scope.sortDir = 'DESC';

            $scope.documents = [];
            getDocs();

            function getDocs() {
                url = siteUrl+'documents/index/'+model_type+'/'+model_id+'?f=l&bust='+Math.random().toString(36).slice(2)
                $http.get(url)
                .then(function(response) {
                    angular.copy(response.data, $scope.documents);
                });
            }
            
            $scope.addDocument = function(e) {
                e.preventDefault();
                var $form = $('#DocumentViewForm');

                var $ctrl = this;

                var file = $ctrl.myFile;
                var uploadUrl = siteUrl+'documents/upload';

                var fd = new FormData();
                fd.append('file', file);
                fd.append('Document[model_type]', angular.element('#DocumentModelType').val());
                fd.append('Document[model_id]', angular.element('#DocumentModelId').val());
                fd.append('Document[remark]', angular.element('#DocumentRemark').val());
                $http.post(uploadUrl, fd, {
                    transformRequest: angular.identity,
                    headers: {'Content-Type': undefined}
                }).success(function(data) {
                    if(data.action == 'success') {
                        $scope.documents.push(data);
                        angular.element('#DocumentRemark').val('');
                        angular.element('#DocumentFile').val('');
                        angular.element('#documentDetails').html('');
                        $ctrl.myFile = '';
                        toastr.success('Document successfully added.');
                    } else {
                        toastr.error(data.msg);
                    }
                })

                return false;
            };

            $scope.removeDocument = function(line_id) {
                $http({
                    method  : 'POST',
                    url     : siteUrl+'documents/delete/'+line_id,
                    data    : {line_id: line_id},
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
                }).success(function(data) {
                    if (data.action == 'success') {
                        angular.forEach($scope.documents, function(value,index){
                            if(value.Document.id == line_id) {
                                $scope.documents.splice(index,1);
                                $scope.documents.splice(index,0);
                            }
                        });
                        toastr.success('Document successfully removed.');
                    } else {
                        toastr.success('Can\'t remove document. Please try againe.');
                    }
                });
                return false;
            };

            $scope.close = function (e) {
                e.preventDefault();
                $modalInstance.dismiss('cancel');
            };
        }*/


        $scope.completeOrder = function(order_id) {
            var modalCompleteInstance = $modal.open({
                templateUrl: siteUrl+'orders/complete/'+order_id+'?f=l&bust='+Math.random().toString(36).slice(2),
                controller: ModalCompleteInstanceCtrl
            });

            modalCompleteInstance.result.then(function (data) {
                
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
                        //self.tableParams.reload();
                        getData();
                        toastr["success"](data.message);
                        $modalInstance.dismiss('cancel');
                    } else {
                        toastr['error'](data.message);
                    }

                    $('#OrderIndexForm').show();
                    $('#multiFunctions').hide();
                });
            };

            $scope.close = function (e) {
                e.preventDefault();
                $modalInstance.dismiss('cancel');
            };
        }

        $scope.toDraftOrder = function(order_id) {
            var modalToDraftInstance = $modal.open({
                templateUrl: siteUrl+'orders/todraft/'+order_id+'?f=l&bust='+Math.random().toString(36).slice(2),
                controller: ModalToDraftInstanceCtrl
            });

            modalToDraftInstance.result.then(function (data) {
                
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
                        //self.tableParams.reload();
                        getData();
                        toastr["success"](data.message);
                        $modalInstance.dismiss('cancel');
                    } else {
                        toastr['error'](data.message);
                    }

                    $('#OrderIndexForm').show();
                    $('#multiFunctions').hide();
                });
            };

            $scope.close = function (e) {
                e.preventDefault();
                $modalInstance.dismiss('cancel');
            };
        }


        $scope.cancelOrder = function(order_id) {
            var modalCompleteInstance = $modal.open({
                templateUrl: siteUrl+'orders/cancel/'+order_id+'?f=l&bust='+Math.random().toString(36).slice(2),
                controller: ModalCancelInstanceCtrl
            });

            modalCompleteInstance.result.then(function (data) {
                
            });
        }

        var ModalCancelInstanceCtrl = function($scope, $modalInstance, $http) {
            $scope.cancel = function(order_id) {
                if($('#confirmReturn').attr('checked') == 'checked') {
                    var return_issue = 1;
                } else {
                    var return_issue = 0;
                }

                $http({
                    method  : 'POST',
                    url     : siteUrl+'orders/cancel/'+order_id,
                    data    : 'order_id='+order_id+'&return_issue='+return_issue,
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
                }).success(function(data) {
                    if (data.action == 'success') {
                        getData();
                        toastr["success"](data.message);
                        $modalInstance.dismiss('cancel');
                    } else {
                        toastr['error'](data.message);
                    }

                    $('#OrderIndexForm').show();
                    $('#multiFunctions').hide();
                });
                return false;
            };

            $scope.close = function (e) {
                e.preventDefault();
                $modalInstance.dismiss('cancel');
            };
        }


        var ModalReleaseInstanceCtrl = function($scope, $modalInstance, $http) {
            // Submit EditProduct Form
            $scope.orderConfirmRelease = function (order_id) {
                $http({
                    method  : 'POST',
                    url     : siteUrl+'salesorders/release/'+order_id,
                    data    : 'order_id='+order_id,
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
                }).success(function(data) {
                    if (data.action == 'success') {
                        $modalInstance.close(data);
                    } else {
                        $modalInstance.close(data);
                        //toastr['error'](data.message);
                    }
                });
            };

            $scope.acceptDontShow = function (order_id) {
                var msg = $("#show_message1").is(":checked") ? 1 : 0;
                $http({
                    method  : 'POST',
                    url     : siteUrl+'salesorders/showReleasePopup',
                    data    : 'message='+msg,
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
                }).success(function(data) {
                    if (data.action == 'dontshow') {
                        $modalInstance.close(data);
                    }
                });
            };

            $scope.close = function (e) {
                e.preventDefault();
                $modalInstance.dismiss('cancel');
            };
        };

        $scope.orderRelease = function(order_id) {
            if($scope.popup == 1) {
                // try release order
                $http({
                    method  : 'POST',
                    url     : siteUrl+'salesorders/release/'+order_id,
                    data    : 'order_id='+order_id,
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
                }).success(function(data) {
                    if (data.action == 'success') {
                        //self.tableParams.reload();
                        getData();
                        toastr["success"](data.message);
                    } else {
                        toastr['error'](data.message);
                    }
                });
            } else {
                // show confirm modal
                var modalReleaseInstance = $modal.open({
                    templateUrl: siteUrl+'orders/confirm_release/'+order_id+'?bust='+Math.random().toString(36).slice(2),
                    controller: ModalReleaseInstanceCtrl
                });

                modalReleaseInstance.result.then(function (data) {
                    if(data.action == 'success') {
                        //self.tableParams.reload();
                        getData();
                        toastr["success"](data.message);
                    } else if(data.action == 'dontshow') {
                        $scope.popup = data.popup;
                        toastr["success"](data.message);
                    } else {
                        toastr["error"](data.message);
                    }

                    $('#OrderIndexForm').show();
                    $('#multiFunctions').hide();
                });
            }
        }

        $scope.cancelRelease = function(order_id) {
            $http({
                method  : 'POST',
                url     : siteUrl+'salesorders/unrelease/'+order_id,
                data    : 'order_id='+order_id,
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data) {
                if (data.action == 'success') {
                    //self.tableParams.reload();
                    getData();
                    toastr["success"](data.message);
                } else {
                    toastr['error'](data.message);
                }

                $('#OrderIndexForm').show();
                $('#multiFunctions').hide();
            });

        }

        $scope.checkOrder = function(order_id) {

            var is_all = true;
            $('.so_checkboxes').each(function() {
                if($(this).attr('checked') != 'checked') {
                    is_all = false;
                }
            });
            $('#selAll').prop('checked', is_all);
            $.uniform.update();

            var checkedVals = $('.so_checkboxes:checkbox:checked').map(function() {
                return this.value;
            }).get();

            $('tr.filter').each(function(){
                if($(this).find('input.so_checkboxes').is(':checked')) {
                    $(this).addClass('bg-grey-steel bg-font-grey-steel');
                } else {
                    $(this).removeClass('bg-grey-steel').removeClass('bg-font-grey-steel');
                }
            });
            if(checkedVals != '') {
                $('#OrderIndexForm').hide();
                $('#multiFunctions').removeClass('hide').show();
            } else {
                $('#OrderIndexForm').show();
                $('#multiFunctions').hide();
            }
        }

        $scope.checkAll = function() {
            if($('#selAll').attr('checked') == 'checked') {
                $('.so_checkboxes').each(function() {
                    $(this).prop('checked', true);
                });
            } else {
                $('.so_checkboxes').each(function() {
                    $(this).prop('checked', false);
                });
            }
            $.uniform.update();

            var checkedVals = $('.so_checkboxes:checkbox:checked').map(function() {
                return this.value;
            }).get();

            $('tr.filter').each(function(){
                if($(this).find('input.so_checkboxes').is(':checked')) {
                    $(this).addClass('bg-grey-steel bg-font-grey-steel');
                } else {
                    $(this).removeClass('bg-grey-steel').removeClass('bg-font-grey-steel');
                }
            });
            
            if(checkedVals != '') {
                $('#OrderIndexForm').hide();
                $('#multiFunctions').removeClass('hide').show();
            } else {
                $('#OrderIndexForm').show();
                $('#multiFunctions').hide();
            }
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

        $scope.schannel = function(line) {
            var $schannel_text;
            if($scope.networks[line.Order.user_id] != undefined) {
                schannel_text = $scope.networks[line.Order.user_id].name +' <i class="fa fa-angle-right"></i> ' + line.Schannel.name;
            } else {
                schannel_text = line.Schannel.name
            }
            return $sce.trustAsHtml(schannel_text);
        }

        $scope.releaseMultiple = function() {
            var checkedVals = $('.so_checkboxes:checkbox:checked').map(function() {
                return this.value;
            }).get();

            $http({
                method  : 'POST',
                url     : siteUrl+'orders/release_multiple',
                data    : 'ajax=1&data[Order][id]='+checkedVals.join(","),
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data) {
                if (data.action == 'success') {
                    //self.tableParams.reload();
                    getData();
                    toastr["success"](data.message);
                } else {
                    //self.tableParams.reload();
                    getData();
                    toastr['error'](data.message);
                }
                $('#OrderIndexForm').show();
                $('#multiFunctions').hide();
            });
            return false;
        }

        $scope.issueMultiple = function() {
            var checkedVals = $('.so_checkboxes:checkbox:checked').map(function() {
                return this.value;
            }).get();

            $http({
                method  : 'POST',
                url     : siteUrl+'orders_lines/multiissue',
                data    : 'ajax=1&status_id=2&data[Order][id]='+checkedVals.join(","),
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data) {
                if (data.action == 'success') {
                    //self.tableParams.reload();
                    getData();
                    toastr["info"]('Request success');

                    var ModalIssueInstance = $modal.open({
                        templateUrl: siteUrl+'orders_lines/multiissue_report/?&bust='+Math.random().toString(36).slice(2),
                        controller: ModalIssueInstanceCtrl,
                        resolve: {
                            reports: function () {
                                var report = new Object();
                                report.success = data.success;
                                report.access_alert = data.access_alert;
                                report.negativ_alert = data.negativ_alert;
                                report.error_alert = data.error_alert;
                                report.part_alert = data.part_alert;
                                return report;
                            }
                        }
                    });

                    ModalIssueInstance.result.then(function (data) {
                        
                    });
                } else {
                    //self.tableParams.reload();
                    getData();
                    toastr['error'](data.msg);
                }
                $('#OrderIndexForm').show();
                $('#multiFunctions').hide();
            });
            return false;
        }

        var ModalIssueInstanceCtrl = function($scope, $modalInstance, $http, $rootScope, reports) {
            $scope.reports = reports;
            //console.log(test);

            $scope.close = function (e) {
                e.preventDefault();
                $modalInstance.dismiss('cancel');
            };
        }

        var ModalWaveInstanceCtrl = function($scope, $modalInstance, $http, orders) {
            var checkedVals = $('.so_checkboxes:checkbox:checked').map(function() {
                return this.value;
            }).get();
            
            $scope.showForm = 1;
            $scope.order_ids = checkedVals.join(",");
            initModal();

            function initModal() {
                angular.forEach(orders, function(value,index){
                    if(checkedVals.includes(value.Order.id)) {
                        if(value.Order.status_id == 14) {
                            $scope.showForm = 0;
                        }
                    }
                });
            }

            $scope.createWave = function (e) {
                // Submit EditProduct Form
                e.preventDefault();
                var $form = $('#createWaveFormM');

                $('#modalFormMsg').html('').addClass('hide');
                $http({
                    method  : 'POST',
                    url     : $form.attr('action'),
                    data    : $form.serialize(),
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
                }).success(function(data) {
                    $('#selAll').prop('checked', false);
                    $('#OrderIndexForm').show();
                    $('#multiFunctions').hide();
                    $modalInstance.close(data);
                });
                return false;
            }

            $scope.startForm = function() {
                $scope.showForm = 1;
            }

            $scope.close = function (e) {
                e.preventDefault();
                $modalInstance.dismiss('cancel');
            };
        };

        $scope.waveAddMultiple = function(){
            var checkedVals = $('.so_checkboxes:checkbox:checked').map(function() {
                return this.value;
            }).get();

            /*var orders_query = '';
            angular.forEach(checkedVals, function(value,index){
                orders_query = 'orders[]='+value+'&'+ orders_query;
            });*/

            if(checkedVals == '') {
                toastr.error('Select your released orders', "", {tapToDismiss: false,closeButton:true, closeHtml: '<button><i class="fa fa-close"></i></button>',timeOut: false,})
            } else {

                var modalWaveInstance = $modal.open({
                    templateUrl: siteUrl+'waves/createWave'+'?bust='+Math.random().toString(36).slice(2),
                    controller: ModalWaveInstanceCtrl,
                    resolve: {
                        orders: function () {
                            return $scope.orders
                        }
                    }
                });

                modalWaveInstance.result.then(function (data) {
                    if(data.status == 'success') {
                        //self.tableParams.reload();
                        getData();
                        toastr["success"](data.message);
                    } else {
                        toastr["error"](data.message);
                    }
                });
            }
        }

        $scope.trashMultiple = function() {
            var checkedVals = $('.so_checkboxes:checkbox:checked').map(function() {
                return this.value;
            }).get();

            if(checkedVals == '') {
                toastr.error('Please select orders', "", {tapToDismiss: false, closeButton:true, closeHtml: '<button><i class="fa fa-close"></i></button>',timeOut: false,})
            } else {
                $http({
                    method  : 'POST',
                    url     : siteUrl+'orders/delete_multiple',
                    data    : 'ajax=1&data[Order][order_id]='+checkedVals.join(","),
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
                }).success(function(data) {
                    if (data.action == 'success') {
                        //self.tableParams.reload();
                        getData();
                        toastr["success"](data.message);
                    } else if(data.action == 'warning') {
                        getData();
                        toastr['warning'](data.message);
                    } else {
                        getData();
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

        $scope.cancelMultiple = function() {
            var checkedVals = $('.so_checkboxes:checkbox:checked').map(function() {
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
                        getData();
                        toastr["success"](data.message);
                    } else if(data.action == 'warning') {
                        getData();
                        toastr['warning'](data.message);
                    } else {
                        getData();
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