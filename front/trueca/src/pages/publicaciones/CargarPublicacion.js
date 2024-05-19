import { ButtonSubmit } from "../components/ButtonSubmit"

const AgregarPublicacion = () => {
	return <>
	 <form method="post">
	<label >Nombre del producto </label>
	<input type="text" />
	<br/>
	<label>Descripción del producto </label>
	<br/>
	<textarea> </textarea>
	<br/>
	<label for="fotos">Seleccionar Fotos:</label>
    <input type="file" id="fotos" name="fotos" accept="image/*" multiple />
    <br/>
	<label> Categoria </label>
	<select>
		<option> Alimentos </option>
		<option> Articulos de limpieza </option>
		<option> Utiles escolares </option>
		<option> Ropa </option>
	</select>
	<br/>
	<label> Seleccionar centos donde le gustaría intercambiar </label>
	<radio>
		<option> Alimentos </option>
		<option> Articulos de limpieza </option>
		<option> Utiles escolares </option>
		<option> Ropa </option>
	</radio>
	<ButtonSubmit text="Subir producto" />
</form>
</>
}

export default AgregarPublicacion;