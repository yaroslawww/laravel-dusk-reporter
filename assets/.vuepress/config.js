const fs = require("fs");
const path = require("path");
const langMessages = require(process.env.VUEPRESS_LANG_MESSAGES || './messages.json')

let sidebar = [
  {title: langMessages.menu.home, path: '/'},
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
                title: `${file}/${subDirFile.replace(/\.[^.]+$/, '')}`,
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
      sidebar.push({title: file.replace(/\.[^.]+$/, ''), path: `/${file}`},)
    }
  });
});

module.exports = {
  base: process.env.VUEPRESS_BASE || '/browser-tests-html/',
  dest: process.env.VUEPRESS_DEST || 'browser-tests-html/',
  title: langMessages.site.title,
  description: langMessages.site.description,
  head: [
    ['link', {rel: "icon", type: "image/png", sizes: "32x32", href: "/favicon/favicon-32x32.png"}],
  ],
  themeConfig: {
    logo: '/head/logo.png',
    search: false,
    nav: [
      {text: langMessages.menu.home, link: '/'},
    ],
    sidebar,
    sidebarDepth: 1,
  }
}
