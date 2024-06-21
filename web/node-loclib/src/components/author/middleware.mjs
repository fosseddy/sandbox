import { findEntity } from "#src/middleware.mjs";
import { AuthorModel } from "./model.mjs";

const findAuthor = findEntity(AuthorModel);

function validateAuthor(req, res, next) {
    const { name, date_of_death, date_of_birth } = req.body;
    const nameParts = name.split(" ");
    let errors = {};

    if (!name || !name.trim()) {
        errors.name = "Name is required";
    } else if (nameParts.length <= 1 || nameParts.length > 2) {
        errors.name = "Wrong name format. Fistname Lastname";
    } else if (name.length < 5 || name.length > 255) {
        errors.name = "Firstname and Lastname must be " +
                      "between 2 and 127 characters long";
    }

    if (!date_of_birth || !date_of_birth.trim()) {
        errors.date_of_birth = "Date of birth is required";
    }

    if (!date_of_death || !date_of_death.trim()) {
        errors.date_of_death = "Date of death is required";
    }

    req.form = { name, date_of_birth, date_of_death, errors };
    next();
}

export { findAuthor, validateAuthor };
