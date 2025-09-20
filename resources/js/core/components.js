// components
import Card from "../Components/Card/Card.vue";
import Form from "../Components/Card/Form.vue";
import Panel from "../Components/Card/Panel.vue";
import Action from "../Components/Ui/Action.vue";
import Alert from "../Components/Ui/Alert.vue";
import Button from "../Components/Ui/Button.vue";
import Check from "../Components/Ui/Check.vue";
import Footext from "../Components/Ui/Footext.vue";
import Input from "../Components/Ui/Input.vue";
import Plain from "../Components/Ui/Plain.vue";
import Router from "../Components/Ui/Router.vue";
import Select from "../Components/Ui/Select.vue";
import Submit from "../Components/Ui/Submit.vue";
import Switch from "../Components/Ui/Switch.vue";
import Table from "../Components/Ui/Table.vue";
import Textarea from "../Components/Ui/Textarea.vue";

export default function (app) {
  app.component("Card", Card);
  app.component("Panel", Panel);
  app.component("Alert", Alert);
  app.component("MTable", Table);
  app.component("Footext", Footext);
  app.component("MButton", Button);
  app.component("MSubmit", Submit);
  app.component("MAction", Action);
  app.component("MCheck", Check);
  app.component("MSwitch", Switch);
  app.component("MInput", Input);
  app.component("MSelect", Select);
  app.component("MTextarea", Textarea);
  app.component("MPlain", Plain);
  app.component("MRouter", Router);
  app.component("MForm", Form);
}
