const path = require("path");
const langMessages = require(process.env.VUEPRESS_LANG_MESSAGES || './messages.json')
const filesManager = require("./build-tools/helpers");

const directoryPath = path.join(`${__dirname}/../`);
const sidebar = filesManager.getFilesRecursive(directoryPath)
    .filter((source) => source.endsWith(process.env.VUEPRESS_DUSK_REPORT_EXT || '.md'))
    .filter((source) => !source.includes("/.vuepress"))
    .map((source) => source.replace(directoryPath, ''))
    .reduce((sidebar, currentVal) => {
        sidebar.push({
                title: `${currentVal.replace(/\.[^.]+$/, '')}`,
                path: `/${currentVal}`
            },
        )
        return sidebar;
    }, [
        {title: langMessages.menu.home, path: '/'},
    ]);

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
