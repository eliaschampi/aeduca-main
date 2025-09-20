import { ago, date, datetim } from "../core/date.js";
import { currency } from "../core/util.js";

export default function(app) {
    const properties = {
        $date: date,
        $datetim: datetim,
        $ago: ago,
        $currency: currency,
    };
    Object.keys(properties).forEach((key) => {
        app.config.globalProperties[key] = properties[key];
    });
}
