import { ButtonSubmit } from "../../components/ButtonSubmit"


export function AgregarCentro() {
	return <div>
			<form method="post">
				<label id="formtext" >Ingrese el nombre del nuevo centro a ingresar </label>
				<br/>
				<input type="text" id="completar" required />
				<br/>
				<label id="formtext" >Ingrese la dirección </label>
				<br/>
				<input type="text" id="completar" required />
				<br/>
				<ButtonSubmit text="Agregar Centro" />
			</form>
		   </div>
}