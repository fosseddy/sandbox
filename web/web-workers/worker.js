console.log("hello from worker.js");

const COUNTER_DELAY = 300;

let counter = 0;
let id = null;

postMessage({ type: "change-counter", payload: counter });

addEventListener("message", event => {
  switch (event.data.type) {
    case "start-counter": {
      if (!id) {
        id = setInterval(() => {
          counter++;
          postMessage({ type: "change-counter", payload: counter });
        }, COUNTER_DELAY);
      }
    } break;

    case "stop-counter": {
      if (id) {
        clearInterval(id);
        id = null;
      }
    } break;

    default:
      console.assert(false, "Unreachable", event.data);
  }
});

