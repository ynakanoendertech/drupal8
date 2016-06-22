(function() {

  "use strict";

  var app = angular.module('myApp', ['ngSanitize', 'ngAnimate', 'ngTouch', 'ui.bootstrap']);

  app.controller('myCtrl', function($scope) {

    // Decode HTML entities
    var tempElem = document.createElement("textarea");
    tempElem.innerHTML = resourceGuideJSON;
    var decodedJSON = tempElem.value;

    // Parse JSON
    $scope.resourceGuideData = JSON.parse(decodedJSON);
    console.dir($scope.resourceGuideData);
  });

})();