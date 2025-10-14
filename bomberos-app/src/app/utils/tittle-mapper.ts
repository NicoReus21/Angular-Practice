export function mapStepTitleToSnakeCase(title: string): string {
    if (title.toLowerCase().startsWith('diab')) {
        return 'diab';
    }

    // Proceso de conversión general
    return title
        .toLowerCase()
        // Elimina caracteres especiales y paréntesis
        .replace(/\(.*?\)|[^\w\s-]/g, '') 
        // Reemplaza espacios y guiones con guiones bajos
        .replace(/[\s-]+/g, '_')
        // Elimina guiones bajos al final del string
        .replace(/_+$/, '');
}
