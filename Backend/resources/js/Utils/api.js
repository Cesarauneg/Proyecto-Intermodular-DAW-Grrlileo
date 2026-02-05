export const API = {
    bugs: {
        list: 'api/bugs',
        filter: 'api/bugs/filter',
        available: 'api/bugs/available',
    },
    fish: {
        list: 'api/fish',
        filter: 'api/fish/filter',
        available: 'api/fish/available',
    },
    villagers: {
        list: 'api/villagers',
        filter: 'api/villagers/filter',
        filters: 'api/villagers/filters',
    },
    art: {
        list: 'api/art',
        filter: 'api/art/filter',
    },
    fossils: {
        list: 'api/fossils',
        filter: 'api/fossils/filter',
    },
    seaCreatures: {
        list: 'api/sea_creatures',
        filter: 'api/sea_creatures/filter',
        available: 'api/sea_creatures/available',
    },
    music: {
        list: 'api/hourly-music',
        byHour: (hour) => `api/hourly-music/hour/${hour}`,
        currentHour: 'api/hourly-music/current-hour',
    },
    user: {
        items: (type) => `user/${type}`,
        villagers: 'user/villagers',
    },
    donate: (type, id) => `${type}/${id}/donate`,
    favorite: (id) => `villagers/${id}/favorite`,
}

