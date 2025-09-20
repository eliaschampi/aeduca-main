// eslint.config.mjs
import js from "@eslint/js";
import pluginVue from "eslint-plugin-vue";
import eslintPluginPrettier from "eslint-plugin-prettier/recommended";
import globals from "globals";

export default [
  // Base configurations
  js.configs.recommended,
  ...pluginVue.configs["flat/recommended"],
  
  // Main configuration
  {
    files: ["**/*.{js,mjs,vue}"],
    languageOptions: {
      ecmaVersion: 2024,
      sourceType: "module",
      globals: {
        ...globals.browser,
        ...globals.es2024,
        ...globals.node,
        _: "readonly",
        defineProps: "readonly",
        defineEmits: "readonly",
        defineExpose: "readonly",
        withDefaults: "readonly",
      },
    },
    rules: {
      // ===== CALIDAD DE CÓDIGO JAVASCRIPT =====
      "no-console": process.env.NODE_ENV === "production" ? "error" : "warn",
      "no-debugger": process.env.NODE_ENV === "production" ? "error" : "warn",
      "no-unused-vars": ["error", {
        argsIgnorePattern: "^_",
        varsIgnorePattern: "^_",
        caughtErrorsIgnorePattern: "^_",
        destructuredArrayIgnorePattern: "^_",
      }],
      "no-empty": ["error", { allowEmptyCatch: true }],
      "prefer-const": "error",
      "no-var": "error",
      "object-shorthand": ["error", "always"],
      "prefer-template": "error",
      "prefer-destructuring": ["error", {
        array: false,
        object: true,
      }],
      "no-nested-ternary": "error",
      "no-unneeded-ternary": "error",
      "prefer-arrow-callback": "error",
      "arrow-body-style": ["error", "as-needed"],
      
      // ===== VUE TEMPLATE =====
      "vue/multi-word-component-names": "off",
      "vue/require-default-prop": "off",
      "vue/no-v-html": "warn",
      "vue/html-indent": ["error", 2, {
        attribute: 1,
        baseIndent: 1,
        closeBracket: 0,
        alignAttributesVertically: true,
      }],
      "vue/html-self-closing": ["error", {
        html: {
          void: "always",
          normal: "never",
          component: "always"
        },
        svg: "always",
        math: "always"
      }],
      "vue/component-name-in-template-casing": ["error", "PascalCase", {
        registeredComponentsOnly: false,
      }],
      "vue/html-closing-bracket-spacing": ["error", {
        startTag: "never",
        endTag: "never",
        selfClosingTag: "always"
      }],
      "vue/html-closing-bracket-newline": ["error", {
        singleline: "never",
        multiline: "always"
      }],
      "vue/max-attributes-per-line": ["error", {
        singleline: 3,
        multiline: 1
      }],
      "vue/first-attribute-linebreak": ["error", {
        singleline: "ignore",
        multiline: "below"
      }],
      "vue/attribute-hyphenation": ["error", "always"],
      "vue/v-on-event-hyphenation": ["error", "always"],
      "vue/prop-name-casing": ["error", "camelCase"],
      
      // ===== VUE SCRIPT =====
      "vue/component-definition-name-casing": ["error", "PascalCase"],
      "vue/no-unused-components": "error",
      "vue/no-unused-vars": "error",
      "vue/no-useless-v-bind": "error",
      "vue/no-useless-mustaches": "error",
      "vue/prefer-separate-static-class": "error",
      "vue/prefer-true-attribute-shorthand": "error",
      "vue/require-explicit-emits": ["error", {
        allowProps: false,
      }],
      "vue/no-empty-component-block": "error",
      "vue/padding-line-between-blocks": ["error", "always"],
      "vue/prefer-import-from-vue": "error",
      "vue/require-macro-variable-name": ["error", {
        defineProps: "props",
        defineEmits: "emit",
        defineSlots: "slots",
        useSlots: "slots",
        useAttrs: "attrs",
      }],
      "vue/valid-v-slot": ["error", {
        allowModifiers: true,
      }],
      
      // ===== VUE BEST PRACTICES =====
      "vue/no-duplicate-attributes": ["error", {
        allowCoexistClass: true,
        allowCoexistStyle: true,
      }],
      "vue/no-template-shadow": "error",
      "vue/one-component-per-file": "error",
      "vue/require-prop-types": "error",
      "vue/require-v-for-key": "error",
      "vue/no-use-v-if-with-v-for": ["error", {
        allowUsingIterationVar: false,
      }],
    },
  },
  
  // Prettier integration - debe ir al final
  eslintPluginPrettier,
  
  // Ignore patterns
  {
    ignores: [
      "**/dist/**",
      "**/build/**",
      "**/node_modules/**",
      "**/.nuxt/**",
      "**/public/**",
      "**/vendor/**",
      "**/*.min.js",
      "**/coverage/**",
      "**/.vscode/**",
      "**/.idea/**",
      "**/storage/**",
      "**/.env*",
    ],
  },
];