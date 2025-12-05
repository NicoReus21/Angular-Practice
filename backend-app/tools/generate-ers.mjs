import { promises as fs } from 'fs';
import path from 'path';

const REQUIREMENTS_DIR = path.resolve('docs', 'requirements');
const OUTPUT_DIR = path.resolve('docs', 'ers');
const ERS_MD = path.join(OUTPUT_DIR, 'ERS.md');
const ERS_PUML = path.join(OUTPUT_DIR, 'ERS.puml');
const ERS_PDF = path.join(OUTPUT_DIR, 'ERS.pdf');
const ERS_DOCX = path.join(OUTPUT_DIR, 'ERS.docx');
const PDF_STYLES = path.join(OUTPUT_DIR, 'pdf-styles.css');
const RNF_FILE = path.join(OUTPUT_DIR, 'rnf.md');
const USERS_DIR = path.resolve('docs', 'users');

const PROJECT_NAME = 'Sistema general de Bomberos Antofagasta';
const INTRO_TEXT =
  'Este documento consolida los requerimientos funcionales a partir de las Tarjetas de Requerimiento vigentes. ' +
  'Incluye un resumen por módulo, el detalle de cada tarjeta y un diagrama tipo mindmap en PlantUML.';

const LABEL_MAP = {
  AUT: 'Autentificación',
  MM: 'Material Mayor',
  PBA: 'Bombero Accidentado',
  SAP: 'Bombero Accidentado',
  COR: 'Bombero Accidentado',
};

const MODULE_ORDER = ['Bombero Accidentado', 'Material Mayor', 'Autentificación'];

const NON_FUNCTIONAL_DEFAULT = [
  { id: 'RNF-01', titulo: 'Seguridad', descripcion: 'Pendiente de documentar.' },
  { id: 'RNF-02', titulo: 'Velocidad', descripcion: 'Pendiente de documentar.' },
  { id: 'RNF-03', titulo: 'Disponibilidad', descripcion: 'Pendiente de documentar.' },
  { id: 'RNF-04', titulo: 'Usabilidad', descripcion: 'Pendiente de documentar.' },
  { id: 'RNF-05', titulo: 'Mantenibilidad', descripcion: 'Pendiente de documentar.' },
  { id: 'RNF-06', titulo: 'Compatibilidad', descripcion: 'Pendiente de documentar.' },
  { id: 'RNF-07', titulo: 'Experiencia de Usuario', descripcion: 'Pendiente de documentar.' },
];

const sanitizeText = (value) => (value || 'N/D').replace(/\|/g, '\\|').trim();

const slugify = (value) =>
  value
    .toString()
    .toLowerCase()
    .normalize('NFD')
    .replace(/\p{Diacritic}/gu, '')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '');

const readRequirements = async () => {
  const files = await fs.readdir(REQUIREMENTS_DIR);
  const candidates = files
    .filter((file) => file.endsWith('.md'))
    .filter((file) => file !== 'README.md' && file !== 'TEMPLATE.md')
    .sort();

  const items = [];

  for (const file of candidates) {
    const fullPath = path.join(REQUIREMENTS_DIR, file);
    const raw = await fs.readFile(fullPath, 'utf8');
    const content = raw.replace(/\r\n/g, '\n');

    const readMeta = (label, fallback = 'N/D') => {
      const regex = new RegExp(`\\*\\*${label}\\s*:\\*\\*\\s*([^\\n]*)`, 'i');
      const match = content.match(regex);
      return match ? match[1].trim() : fallback;
    };

    const splitSections = content.split(/\n---\n/);
    const descriptionBlock = splitSections[1] ?? '';
    const validationsBlock = splitSections[2] ?? '';
    const statusBlock = splitSections[3] ?? '';

    const cleanSection = (section) =>
      section
        .replace(/^\s*##[^\n]*\n*/i, '')
        .replace(/\n{3,}/g, '\n\n')
        .trim();

    const readStatus = (label, fallback = 'N/D') => {
      const regex = new RegExp(`\\*\\*${label}[^:]*:\\*\\*\\s*([^\\n]*)`, 'i');
      const match = statusBlock.match(regex);
      return match ? match[1].trim() : fallback;
    };

    const id = readMeta('ID', path.basename(file, '.md'));
    const prefix = id.split('-')[0] || 'REQ';

    items.push({
      id,
      prefix,
      usuario: readMeta('Usuario'),
      nombre: readMeta('Nombre del Requerimiento'),
      programador: readMeta('Programador Responsable'),
      iteracion: readMeta('Iteraci[óo]n Asignada', 'N/D'),
      descripcion: cleanSection(descriptionBlock),
      validaciones: cleanSection(validationsBlock),
      estado: readStatus('Estado inicial', 'Pendiente'),
      ultimaActualizacion: readStatus('Ultima actualizaci', 'N/D'),
    });
  }

  return items;
};

const readUsers = async () => {
  const map = new Map();
  try {
    const files = await fs.readdir(USERS_DIR);
    const candidates = files
      .filter((file) => file.endsWith('.md'))
      .filter((file) => file !== 'README.md' && file !== 'TEMPLATE.md');

    for (const file of candidates) {
      const fullPath = path.join(USERS_DIR, file);
      const raw = await fs.readFile(fullPath, 'utf8');
      const content = raw.replace(/\r\n/g, '\n');

      const readMeta = (label, fallback = 'N/D') => {
        const regex = new RegExp(`\\*\\*${label}\\s*:\\*\\*\\s*([^\\n]*)`, 'i');
        const match = content.match(regex);
        return match ? match[1].trim() : fallback;
      };

      const name = readMeta('Nombre', path.basename(file, '.md'));
      const descripcion = readMeta('Descripción', 'Pendiente de documentar');

      map.set(name, { descripcion });
    }
  } catch (error) {
    console.warn(`No se pudieron cargar usuarios desde ${USERS_DIR}; se usará texto por defecto.`);
  }

  return map;
};

const ensureOutputDir = async () => {
  await fs.mkdir(OUTPUT_DIR, { recursive: true });
};

const loadNonFunctional = async () => {
  try {
    const raw = await fs.readFile(RNF_FILE, 'utf8');
    const lines = raw.replace(/\r\n/g, '\n').split('\n');
    const items = [];

    for (const line of lines) {
      const trimmed = line.trim();
      if (!trimmed.startsWith('-')) continue;
      const match = trimmed.match(
        /^-\s*\*\*(?<id>[^*]+)\*\*\s*[-–—:]?\s*(?<rest>.+)$/i
      );
      if (!match) continue;
      const id = match.groups.id.trim();
      const rest = match.groups.rest.trim();
      let titulo = rest;
      let descripcion = 'Pendiente de documentar.';

      const parts = rest.split(/:\s+/, 2);
      if (parts.length === 2) {
        titulo = parts[0].trim();
        descripcion = parts[1].trim() || descripcion;
      } else if (rest.includes(' - ')) {
        const [t, d] = rest.split(' - ', 2);
        titulo = t.trim();
        descripcion = d?.trim() || descripcion;
      }

      items.push({ id, titulo, descripcion });
    }

    return items.length ? items : NON_FUNCTIONAL_DEFAULT;
  } catch (error) {
    console.warn(`No se encontró RNF en ${RNF_FILE}, usando valores por defecto.`);
    return NON_FUNCTIONAL_DEFAULT;
  }
};

const buildUsersTable = (requirements, users = new Map()) => {
  const roles = new Set();
  requirements.forEach((req) => {
    req.usuario
      .split(',')
      .map((role) => role.trim())
      .filter(Boolean)
      .forEach((role) => roles.add(role));
  });

  const lines = [];
  lines.push('| Usuario | Descripción |');
  lines.push('| --- | --- |');
  roles.forEach((role) => {
    const descripcion = users.get(role)?.descripcion || 'Pendiente de documentar';
    lines.push(`| ${sanitizeText(role)} | ${sanitizeText(descripcion)} |`);
  });
  return lines.join('\n');
};

const formatBlock = (text) => {
  if (!text || !text.trim()) return 'N/D';
  const parts = text
    .split(/\n+/)
    .map((t) => t.trim())
    .filter(Boolean);

  const looksLikeList = parts.some((p) => /^[-*]\s/.test(p));
  if (looksLikeList) {
    const items = parts.map((p) => p.replace(/^[-*]\s*/, ''));
    return `<ul>${items.map((item) => `<li>${item}</li>`).join('')}</ul>`;
  }
  return parts.join('<br>');
};

const requirementTable = (req) => {
  return `
<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> ${sanitizeText(req.id)}</td>
    <td><strong>Usuario(s):</strong> ${sanitizeText(req.usuario)}</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> ${sanitizeText(req.programador)}</td>
    <td><strong>Iteración Asignada:</strong> ${sanitizeText(req.iteracion)}</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> ${sanitizeText(req.estado)}</td>
    <td><strong>Última actualización:</strong> ${sanitizeText(req.ultimaActualizacion)}</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> ${sanitizeText(req.nombre)}</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>${formatBlock(req.descripcion)}</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br>${formatBlock(req.validaciones)}</td>
  </tr>
</table>
`.trim();
};

const sortLabels = (labels) =>
  [...labels].sort((a, b) => {
    const ia = MODULE_ORDER.indexOf(a);
    const ib = MODULE_ORDER.indexOf(b);
    if (ia !== -1 && ib !== -1) return ia - ib;
    if (ia !== -1) return -1;
    if (ib !== -1) return 1;
    return a.localeCompare(b);
  });

const buildRequirementCards = (requirements) => {
  const grouped = new Map();
  for (const req of requirements) {
    const label = LABEL_MAP[req.prefix] || req.prefix || 'Otros';
    if (!grouped.has(label)) grouped.set(label, []);
    grouped.get(label).push(req);
  }

  for (const list of grouped.values()) {
    list.sort((a, b) => a.id.localeCompare(b.id));
  }

  const groups = Array.from(grouped.entries()).sort((a, b) => {
    const order = sortLabels([a[0], b[0]]);
    return order[0] === a[0] ? -1 : 1;
  });

  const lines = [];
  lines.push('## II. Requerimientos');

  for (const [label, list] of groups) {
    lines.push('');
    lines.push(`### Módulo: ${label}`);
    lines.push('');
    for (const req of list) {
      lines.push(`#### Tarjeta de Requerimiento — ID: ${req.id}`);
      lines.push('');
      lines.push(requirementTable(req));
      lines.push('');
      lines.push('<div style="page-break-after: always;"></div>');
      lines.push('');
    }
  }

  return lines.join('\n');
};

const buildNonFunctional = (nonFunctional) => {
  const lines = [];
  lines.push('## III. Requerimientos no funcionales');
  lines.push('');
  nonFunctional.forEach((item) => {
    lines.push(`- **${item.id} – ${item.titulo}:** ${item.descripcion}`);
  });
  lines.push('');
  return lines.join('\n');
};

const buildFlowSection = () => {
  const lines = [];
  lines.push('## IV. Flujo del sistema');
  lines.push('');
  lines.push(
    'El diagrama de relación de requerimientos se encuentra en `docs/ers/ERS.puml` y se renderiza a PNG como `docs/ers/ERS.png`.'
  );
  lines.push('');
  return lines.join('\n');
};

const buildToc = (modules) => {
  const lines = [];
  lines.push('## Tabla de contenido');
  lines.push('- [I. Introducción](#i-introduccion)');
  lines.push('  - [1. Usuarios](#1-usuarios)');
  lines.push('- [II. Requerimientos](#ii-requerimientos)');
  modules.forEach((label) => {
    const slug = slugify(`Módulo ${label}`);
    lines.push(`  - [Módulo: ${label}](#${slug})`);
  });
  lines.push('- [III. Requerimientos no funcionales](#iii-requerimientos-no-funcionales)');
  lines.push('- [IV. Flujo del sistema](#iv-flujo-del-sistema)');
  lines.push('');
  lines.push('<div style="page-break-after: always;"></div>');
  return lines.join('\n');
};

const buildMarkdown = (requirements, nonFunctional, users) => {
  const groupedLabels = sortLabels(
    new Set(
      requirements.map((req) => LABEL_MAP[req.prefix] || req.prefix || 'Otros')
    )
  );

  const lines = [];
  const now = new Date().toISOString();

  lines.push('# Especificación de Requerimientos de Software');
  lines.push(`_Proyecto: ${PROJECT_NAME}_`);
  lines.push(`Generado automáticamente: ${now}`);
  lines.push('');
  lines.push('<div style="page-break-after: always;"></div>');
  lines.push('');

  lines.push(buildToc(groupedLabels));

  lines.push('## I. Introducción');
  lines.push('');
  lines.push(INTRO_TEXT);
  lines.push('');
  lines.push('### 1. Usuarios');
  lines.push('');
  lines.push(buildUsersTable(requirements, users));
  lines.push('');
  lines.push('<div style="page-break-after: always;"></div>');
  lines.push('');

  lines.push(buildRequirementCards(requirements));
  lines.push(buildNonFunctional(nonFunctional));
  lines.push(buildFlowSection());

  return lines.join('\n');
};

const buildPlantUml = (requirements) => {
  const grouped = new Map();
  for (const req of requirements) {
    const label = LABEL_MAP[req.prefix] || req.prefix || 'Otros';
    if (!grouped.has(label)) grouped.set(label, []);
    grouped.get(label).push(req);
  }

  for (const list of grouped.values()) {
    list.sort((a, b) => a.id.localeCompare(b.id));
  }

  const groups = Array.from(grouped.entries()).sort((a, b) => {
    const order = sortLabels([a[0], b[0]]);
    return order[0] === a[0] ? -1 : 1;
  });

  const lines = [];
  lines.push('@startmindmap');
  lines.push('* ERS');
  for (const [label, list] of groups) {
    lines.push(`** ${label}`);
    for (const req of list) {
      lines.push(`*** ${req.id} - ${req.nombre}`);
    }
  }
  lines.push('@endmindmap');

  return lines.join('\n');
};

const buildPdfIfPossible = async (markdown) => {
  let mdToPdf;
  try {
    ({ mdToPdf } = await import('md-to-pdf'));
  } catch (error) {
    console.warn('md-to-pdf no está instalado; se omite la generación de PDF.');
    return;
  }

  const result = await mdToPdf(
    { content: markdown },
    {
      dest: ERS_PDF,
      stylesheet: PDF_STYLES,
      pdf_options: {
        format: 'A4',
        printBackground: true,
        margin: {
          top: '20mm',
          right: '20mm',
          bottom: '20mm',
          left: '20mm',
        },
      },
    }
  );

  if (!result || !result.filename) {
    console.warn('No se pudo generar el PDF.');
    return;
  }
  console.log(`ERS PDF generado: ${result.filename}`);
};

const buildDocxIfPossible = async (markdown) => {
  let mdToPdf;
  try {
    ({ mdToPdf } = await import('md-to-pdf'));
  } catch (error) {
    console.warn('md-to-pdf no estÇ­ instalado; se omite la generaciÇün de DOCX.');
    return;
  }

  let htmlToDocx;
  try {
    ({ default: htmlToDocx } = await import('html-to-docx'));
  } catch (error) {
    console.warn('html-to-docx no estÇ­ instalado; se omite la generaciÇün de DOCX.');
    return;
  }

  let htmlContent = '';
  try {
    const result = await mdToPdf(
      { content: markdown },
      { as_html: true, stylesheet: PDF_STYLES }
    );
    htmlContent = typeof result?.content === 'string' ? result.content : '';
  } catch (error) {
    console.warn('No se pudo generar el HTML base para DOCX.');
    return;
  }

  if (!htmlContent) {
    console.warn('El HTML generado para DOCX estÇü vacÇ­o; se omite la exportaciÇün.');
    return;
  }

  try {
    const buffer = await htmlToDocx(htmlContent);
    await fs.writeFile(ERS_DOCX, buffer);
    console.log(`ERS DOCX generado: ${ERS_DOCX}`);
  } catch (error) {
    console.warn(`No se pudo generar el DOCX: ${error.message}`);
  }
};

const ensurePdfStyles = async () => {
  try {
    await fs.access(PDF_STYLES);
  } catch {
    const css = `
body {
  font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
  line-height: 1.5;
  color: #111827;
}
h1, h2, h3, h4 {
  color: #0f172a;
  font-weight: 700;
}
code {
  font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', monospace;
}
table {
  width: 100%;
  border-collapse: collapse;
}
table th, table td {
  border: 1px solid #e5e7eb;
  padding: 8px;
}
table th {
  background: #f3f4f6;
}
div {
  page-break-inside: avoid;
}
`;
    await fs.writeFile(PDF_STYLES, css.trim(), 'utf8');
  }
};

const main = async () => {
  await ensureOutputDir();
  await ensurePdfStyles();
  const requirements = await readRequirements();
  const nonFunctional = await loadNonFunctional();
  const users = await readUsers();
  const markdown = buildMarkdown(requirements, nonFunctional, users);
  const plantuml = buildPlantUml(requirements);

  await fs.writeFile(ERS_MD, markdown, 'utf8');
  await fs.writeFile(ERS_PUML, plantuml, 'utf8');
  await buildPdfIfPossible(markdown);
  await buildDocxIfPossible(markdown);

  console.log(`ERS generada: ${ERS_MD}`);
  console.log(`PlantUML generado: ${ERS_PUML}`);
};

main().catch((error) => {
  console.error('Error generando ERS:', error);
  process.exit(1);
});
