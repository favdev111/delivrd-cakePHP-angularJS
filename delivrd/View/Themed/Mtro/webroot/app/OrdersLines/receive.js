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

var ReceiveProducts = function($scope, $http, $modal, $sce) {

	$scope.addReceiveNotes = function(line_id) {
        var modalAddReceiveNotesInstance = $modal.open({
            templateUrl: siteUrl+'orders_lines/add_receive_notes/'+line_id+'?bust='+Math.random().toString(36).slice(2),
            controller: ModalAddReceiveNotesInstanceCtrl
        });

        modalAddReceiveNotesInstance.result.then(function (data) {
            if(data.action == 'success') {
                toastr["success"](data.message);
            } else {
                toastr["error"](data.message);
            }
        });
    }

    var ModalAddReceiveNotesInstanceCtrl = function($scope, $modalInstance, $http) {

        $scope.addNote = function (e) {
            e.preventDefault();
            var $form = $('#OrdersLineAddReceiveNotesForm');

            $('#modalFormMsg').html('').addClass('hide');
            $http({
                method  : 'POST',
                url     : $form.attr('action'),
                data    : $form.serialize(),
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data) {
                if (data.action == 'success') {
                	var lineId = $('#OrdersLineId').val();
                	$("#receivenotes-" + lineId).val($('#OrdersLineReceivenotes').val());
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
}