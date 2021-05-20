const fs = require("fs");
const path = require("path");

let sidebar = [
    {title: 'Home', path: '/'},
]
const directoryPath = path.join(`${__dirname}/../`);
fs.readdir(directoryPath, function (err, files) {
    if (err) {
        return console.log('Unable to scan directory: ' + err);
    }
    files.forEach(function (file) {
        if (fs.statSync(path.join(directoryPath, file)).isDirectory() && !file.startsWith('.')) {
            let subDirectoryPath = path.join(directoryPath, file)
            fs.readdir(subDirectoryPath, function (err, subDirFiles) {
                if (err) {
                    return console.log('Unable to scan directory: ' + err);
                }
                subDirFiles.forEach(function (subDirFile) {
                    if (
                        fs.statSync(path.join(subDirectoryPath, subDirFile)).isFile()
                        && !subDirFile.startsWith('.')
                        && subDirFile.endsWith('.md')
                    ) {
                        sidebar.push({
                                title: `${file}/${subDirFile.replace(/\.[^.]+$/, '.test')}`,
                                path: `/${file}/${subDirFile}`
                            },
                        )
                    }
                });
            });
        } else if (
            fs.statSync(path.join(directoryPath, file)).isFile()
            && file !== 'index.md'
            && !file.startsWith('.')
            && file.endsWith('.md')
        ) {
            sidebar.push({title: file.replace(/\.[^.]+$/, '.test'), path: `/${file}`},)
        }
    });
});

module.exports = {
    base: process.env.VUEPRESS_BASE || '/browser-tests-html/',
    dest: process.env.VUEPRESS_DEST || 'browser-tests-html/',
    title: 'Browser tests result',
    description: 'Browser tests',
    head: [
        ['link', {rel: "icon", type: "image/png", sizes: "32x32", href: "/favicon/favicon-32x32.png"}],
    ],
    themeConfig: {
        logo: '/head/logo.png',
        search: false,
        nav: [
            {text: 'Home', link: '/'},
        ],
        sidebar,
        sidebarDepth: 1,
    }
}
