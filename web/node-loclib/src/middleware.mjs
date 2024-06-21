function findEntity(model, name = "") {
    return async function (req, res, next) {
        if (!req.params.id) {
            throw new Error("No :id param");
        }

        const { id } = req.params;
        const entity = await model.find({ id });

        if (!entity) {
            throw new Error(`Entity(${model.table}) with id ${id} not found`);
        }

        name = name || model.table;

        req[name] = entity;
        next();
    }
}

export { findEntity };
