// eslint.config.mjs
import js from "@eslint/js";
import pluginVue from "eslint-plugin-vue";
import globals from "globals";

export default [
  js.configs.recommended,
  ...pluginVue.configs["flat/recommended"],
  {
    files: ["**/*.{js,vue}"],
    languageOptions: {
      ecmaVersion: "latest",
      sourceType: "module",
      globals: {
        ...globals.browser,
        ...globals.es2021,
        _: "readonly", // Lodash global
      },
    },
    rules: {
      // Code Quality
      "no-console": process.env.NODE_ENV === "production" ? "error" : "off",
      "no-debugger": process.env.NODE_ENV === "production" ? "error" : "off",
      "no-unused-vars": ["error", {
        argsIgnorePattern: "^_",
        varsIgnorePattern: "^_",
        caughtErrorsIgnorePattern: "^_"
      }],

      // JavaScript Style
      "semi": ["error", "always"],
      "quotes": ["error", "double", { avoidEscape: true }],
      "comma-dangle": ["error", "always-multiline"],
      "arrow-parens": ["error", "always"],
      "object-curly-spacing": ["error", "always"],
      "array-bracket-spacing": ["error", "never"],
      "space-before-function-paren": ["error", {
        anonymous: "never",
        named: "never",
        asyncArrow: "always"
      }],

      // Vue Specific
      "vue/multi-word-component-names": "off",
      "vue/require-default-prop": "off",
      "vue/html-indent": ["error", 4],
      "vue/html-self-closing": ["error", {
        html: {
          void: "always",
          normal: "never",
          component: "always"
        },
        svg: "always",
        math: "always"
      }],
      "vue/component-name-in-template-casing": ["error", "PascalCase"],
      "vue/singleline-html-element-content-newline": "off",
      "vue/max-attributes-per-line": ["error", {
        singleline: 4,  // Allow up to 4 attributes on single line
        multiline: 1
      }],
      "vue/first-attribute-linebreak": ["error", {
        singleline: "ignore",  // Don't force linebreak for single line
        multiline: "below"
      }],
      "vue/attribute-hyphenation": ["error", "always"],
      "vue/v-on-event-hyphenation": ["error", "always"],
      "vue/html-closing-bracket-spacing": ["error", {
        startTag: "never",
        endTag: "never",
        selfClosingTag: "always"
      }],
      "vue/html-closing-bracket-newline": ["error", {
        singleline: "never",
        multiline: "always"
      }],
    },
  },
  {
    ignores: [
      "**/dist/**",
      "**/build/**",
      "**/node_modules/**",
      "**/.nuxt/**",
      "**/public/**",
      "**/vendor/**",
      "**/*.min.js",
      "**/coverage/**"
    ],
  },
];