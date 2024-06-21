import { GenreModel } from "./model.mjs";
import { isFormValid } from "#src/validator.mjs";

export async function indexView(req, res) {
    const genres = await GenreModel.findAll();
    res.render("genre/index", { genres });
}

export function showView(req, res) {
    res.render("genre/show", { genre: req.genre });
}

export function createView(req, res) {
    res.render("genre/create", {
        form: { name: "", errors: {} }
    });
}

export function updateView(req, res) {
    res.render("genre/update", {
        form: { name: req.genre.name, errors: {} }
    });
}

export function removeView(req, res) {
    res.render("genre/delete", { genre: req.genre });
}

export async function create(req, res) {
    const { form } = req;

    if (!isFormValid(form)) {
        return res.render("genre/create", { form });
    }

    const id = await GenreModel.insert({ name: form.name });
    res.redirect(`/catalog/genre/${id}`);
}

export async function update(req, res) {
    const { form } = req;

    if (!isFormValid(form)) {
        return res.render("genre/update", { form });
    }

    await GenreModel.update(req.params.id, { name: form.name });
    res.redirect(`/catalog/genre/${req.params.id}`);
}

export async function remove(req, res) {
    const { id } = req.body;

    if (id != req.params.id) {
        throw new Error(`Genre with id ${id} does not exist`);
    }

    await GenreModel.remove(id);

    res.redirect("/catalog/genre");
}
