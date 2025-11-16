export default {
  plugins: {
    autoprefixer: {
      overrideBrowserslist: [
        'last 2 versions',
        '> 1%',
        'not dead',
        'iOS >= 10',
        'Safari >= 10',
        'Firefox ESR',
      ],
      grid: 'autoplace',
      flexbox: 'no-2009',
    },
  },
};
