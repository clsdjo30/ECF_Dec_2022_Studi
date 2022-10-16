import {Notyf} from 'notyf';
import 'notyf/notyf.min.css';

var notyf = new Notyf();
let test = notyf.success('TEST DE TEST QUI MARCHE PAS');

console.log(test);