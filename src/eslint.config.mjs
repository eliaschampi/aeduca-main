import js from "@eslint/js";
import pluginVue from "eslint-plugin-vue";

export default [
  js.configs.recommended,
  ...pluginVue.configs['flat/recommended'],
  {
    files: ["**/*.js", "**/*.vue"],
    languageOptions: {
      ecmaVersion: 2021,
      sourceType: "module",
      globals: {
        _: true,
      },
    },
    rules: {
      "no-console": process.env.NODE_ENV === "production" ? "warn" : "off",
      "no-debugger": process.env.NODE_ENV === "production" ? "warn" : "off",
      "vue/require-default-prop": "off",
      "vue/html-indent": ["error", 4],
      "vue/singleline-html-element-content-newline": 0,
      "vue/component-name-in-template-casing": ["error", "PascalCase"],
      semi: ["error", "always"],
      quotes: ["error", "double"],
      "vue/multi-word-component-names": "off",
    }
  }
]