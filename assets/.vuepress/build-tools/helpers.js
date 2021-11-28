const fs = require('fs');
const path = require('path');

const fileExists = (source) => fs.existsSync(source);

const isDirectory = (source) => fs.lstatSync(source)
    .isDirectory();

const isFile = (source) => fs.lstatSync(source)
    .isFile();

const getDirectories = (source) => fs.readdirSync(source)
    .map((name) => path.join(source, name))
    .filter(isDirectory);

const getDirectoriesRecursive = (source) => getDirectories(source)
    .reduce((result, currentVal) => {
        result.push(currentVal);
        result.push(...getDirectoriesRecursive(currentVal));
        return result;
    }, []);

const getFiles = (source) => fs.readdirSync(source)
    .map((name) => path.join(source, name))
    .filter(isFile);

const getFilesRecursive = (source) => getDirectoriesRecursive(source)
    .reduce((result, currentVal) => {
        result.push(...getFiles(currentVal));
        return result;
    }, []);

module.exports = {
    fileExists,
    isDirectory,
    getDirectories,
    getDirectoriesRecursive,
    isFile,
    getFiles,
    getFilesRecursive,
};
