"use strict";

/**
 * @function getByProp
 * @memberOf Array
 * @param {String} property - the name of the objects property
 * @param {String} value - the value we want to match
 * @description
 *  Returns the first occurrence into an array of objects,
 *  that matches the given value at the given property
 * */
Array.prototype.getByProp = function(property, value) {
  for (var ind = 0; ind < this.length; ++ind) {
    if (this[ind].hasOwnProperty(property)) {
      if (this[ind][property] === value) {
        return this[ind];
      }
    }
  }
  return null;
};

/**
 * @ngdoc method
 * @name getAllByProp
 * @param {String} property - the name of the objects property
 * @param {String} value - the value we want it to equal
 * @description
 *  Returns all occurrences into an array of objects,
 *  that match the given value at the given property
 * */
Array.prototype.getAllByProp = function(property, value) {
  var selectedObjects = [];
  for (var ind = 0; ind < this.length; ++ind) {
    if (this[ind].hasOwnProperty(property)) {
      if (this[ind][property] === value) {
        return selectedObjects.push(this[ind]);
      }
    }
  }
  return selectedObjects;
};

/**
 * @function getById
 * @memberOf Array
 * @param {String} id - the value of the ID we want to match
 * @description
 *  Returns the first occurrence of an item that matches the given ID
 *  from an array of objects that have an ID property
 * */
Array.prototype.getById = function(id) {
  return this.getByProp('id', id);
};

/**
 * @function getIndexById
 * @memberOf Array
 * @param {String} id - the id of the object we want to find
 * @description
 *  gets the index of an object by its id.
 * */
Array.prototype.getIndexById = function(id) {
  return this.indexOf(this.getById(id));
};

/**
 * @function removeById
 * @memberOf Array
 * @param {String} id - the id of the object we want to find
 * @description
 *  Removes the first occurrence of an object into an array of objects by its ID.
 * */
Array.prototype.removeById = function(id) {
  var ind = this.getIndexById(id);
  if (ind >= 0) {
    this.splice(ind, 1);
  }
};
//
// /**
//  * @function removeByProp
//  * @memberOf Array
//  * @param {String} property - property on object
//  * @param {String} value - value for comparison
//  * @description
//  *  removes an object if its property is true.
//  * */
// Array.prototype['removeByProp'] = function(property:string, value:string) {
//   let elem = this.getByProp(property, value);
//   if(!!elem){
//     let idx = this.indexOf(elem);
//     if(idx >= 0){
//       this.splice(idx , 1);
//     }
//   }
//   return;
// };
//
// /**
//  * @function elementInArray
//  * @memberOf Array
//  * @param {String} property - the name of the objects property
//  * @param {String} value - the value we want it to equal
//  * @description
//  *  returns an true if its in a list and its property is equal to the value.
//  * */
// Array.prototype['elementInArray'] = function(property:string, value:string):boolean {
//   for(let i = 0; i < this.length; ++i){
//     if(this[i].hasOwnProperty(property)){
//       if(this[i][property] === value){
//         return true
//       }
//     }
//   }
//   return false;
// };
//
// /**
//  * @function existsById
//  * @memberOf Array
//  * @param {String} id - the id of the object
//  * */
// Array.prototype['existsById'] = function(id:string):boolean {
//   return !!this.getById(id);
// };
//
