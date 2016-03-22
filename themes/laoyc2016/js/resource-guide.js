(function() {

  "use strict";

  var app = angular.module('myApp', []);

  app.controller('myCtrl', function($scope) {

    // Decode HTML entities
    var tempElem = document.createElement("textarea");
    tempElem.innerHTML = resourceGuideJSON;
    var decodedJSON = tempElem.value;
    
    $scope.data = JSON.parse(decodedJSON);
    console.dir($scope.data);
  });

})();