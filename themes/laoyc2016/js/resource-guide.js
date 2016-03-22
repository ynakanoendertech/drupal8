(function() {

  "use strict";

  var app = angular.module('myApp', []);

  app.controller('myCtrl', function($scope) {
    $scope.resourceGuideData = JSON.parse(resourceGuideJSON.replace(/&quot;/g,'"'));
    console.dir($scope.resourceGuideData);
  });

})();