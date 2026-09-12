const LOGOS = [
    { match: /laravel|blade/i, file: 'laravel.svg', color: '#FF2D20' },
    { match: /php/i, file: 'php.svg', color: '#777BB4' },
    { match: /typescript|\bts\b/i, file: 'typescript.svg', color: '#3178C6' },
    { match: /graphql/i, file: 'graphql.svg', color: '#E10098' },
    { match: /codeigniter/i, file: 'codeigniter.svg', color: '#EE4623' },
    { match: /mysql/i, file: 'mysql.svg', color: '#4479A1' },
    { match: /mariadb/i, file: 'mariadb.svg', color: '#4E9BCD' },
    { match: /mongo/i, file: 'mongodb.svg', color: '#47A248' },
    { match: /redis/i, file: 'redis.svg', color: '#DC382D' },
    { match: /tailwind/i, file: 'tailwindcss.svg', color: '#06B6D4' },
    { match: /vue/i, file: 'vuejs.svg', color: '#4FC08D' },
    { match: /react/i, file: 'react.svg', color: '#61DAFB' },
    { match: /\bgit\b/i, file: 'git.svg', color: '#F05032' },
    { match: /docker/i, file: 'docker.svg', color: '#2496ED' },
    { match: /aws/i, file: 'aws.svg', color: '#FF9900' },
    { match: /bootstrap/i, file: 'bootstrap.svg', color: '#7952B3' },
    { match: /javascript|\bjs\b/i, file: 'javascript.svg', color: '#F7DF1E' },
    { match: /html/i, file: 'html5.svg', color: '#E34F26' },
    { match: /css/i, file: 'css3.svg', color: '#1572B6' },
    { match: /npm/i, file: 'npm.svg', color: '#CB3837' },
    { match: /composer/i, file: 'composer.svg', color: '#885630' },
    { match: /postman/i, file: 'postman.svg', color: '#FF6C37' },
    { match: /vs\s?code|vscode/i, file: 'vscode.svg', color: '#007ACC' },
    { match: /linux/i, file: 'linux.svg', color: '#FCC624' },
    { match: /nginx/i, file: 'nginx.svg', color: '#009639' },
];

export function techMeta(name) {
    const match = LOGOS.find((item) => item.match.test(String(name || '')));

    if (!match) {
        return { src: null, color: '#888888' };
    }

    return {
        src: `/images/tech-logos/${match.file}`,
        color: match.color,
    };
}
