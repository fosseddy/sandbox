import { Router } from "express";
import * as controller from "./controller.mjs";
import { findAuthor, validateAuthor } from "./middleware.mjs";

const router = Router();

router.get("/", controller.indexView);

router.get("/:id(\\d+)", findAuthor, controller.showView);

router.route("/create")
    .get(controller.createView)
    .post(validateAuthor, controller.create);

router.route("/:id(\\d+)/update")
    .get(findAuthor, controller.updateView)
    .post(validateAuthor, controller.update);

router.route("/:id(\\d+)/delete")
    .get(findAuthor, controller.removeView)
    .post(controller.remove);

export { router };
