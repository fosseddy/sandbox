import { Router } from "express";
import { GenreModel } from "#components/genre/model.mjs";
import { AuthorModel } from "#components/author/model.mjs";

const router = Router();

router.get("/", async (req, res) => {
    let counts = {};
    counts.genre = await GenreModel.count();
    counts.author = await AuthorModel.count();

    res.render("catalog/index", { counts });
});

export { router };
