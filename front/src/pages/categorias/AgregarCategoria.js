import { ButtonSubmit } from "../../components/ButtonSubmit"

const AgregarCategoria = () => {
	return <div>
			<form method="post">
				<label id="formtext" >Ingrese el nombre de la categoria a agregar! </label>
				<br />
				<input type="text" id="completar" required />
				<br />

				<ButtonSubmit text="Agregar categoria" />
			</form>
		   </div>
}

export default AgregarCategoria;
