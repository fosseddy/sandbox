import { findEntity } from "#src/middleware.mjs";
import { GenreModel } from "./model.mjs";

const findGenre = findEntity(GenreModel);

function validateGenre(req, res, next) {
    const { name } = req.body;
    let errors = {};

    if (!name || !name.trim()) {
        errors.name = "Name is required";
    } else if (name.length < 2 || name.length > 255) {
        errors.name = "Name must be between 2 and 255 characters long";
    }

    req.form = { name, errors };
    next();
}

export { findGenre, validateGenre };
