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

app.controller( 'modalDocumentInstanceCtrl', ['$scope', '$modalInstance', '$http', 'fileUpload', 'model_id', 'model_type', 'model_name', function ($scope, $modalInstance, $http, fileUpload, model_id, model_type, model_name) {

    $scope.title = doc_title+' '+model_name;

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
}]);