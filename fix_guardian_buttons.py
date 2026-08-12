"""Repair the Guardian buttons on the student form.

Five things were wrong, all proved in a browser against a real student record:

  1. Every one of the five buttons ended with lines like

         f.father_as_guardian.checked == false;

     wrong twice over. The radios are a single group named guardian_is - there is no field
     called father_as_guardian - and '==' compares, it does not assign. So the lines never did
     anything, and reaching them threw
     "TypeError: Cannot read properties of undefined (reading 'checked')",
     which stopped the function where it stood. A browser unchecks the other radios in a group
     by itself, so the lines are simply removed.

  2. linkGuardian() was declared with no parameter while the markup calls linkGuardian(this.form).
     Every line in it then read an undefined f: "ReferenceError: f is not defined". Link Guardian
     copied nothing at all.

  3. The buttons set different sets of fields. Father and Mother filled occupation, office and
     eligibility; Self did not. Switching a student from Father to Self left the father's
     occupation under the student's own name - checked on a live record: name and mobile became
     the student's, occupation stayed "FARMER" - and it saved that way.

  4. Other's cleared every guardian field except the address, so a brand new guardian kept the
     previous one's address.

  5. Nothing guarded against a field being absent, so one missing input took the whole button
     down.

They all go through one function now, which writes every guardian field every time. That is
what makes a stale value impossible rather than merely corrected today.

Applied to both copies: the office form and the public online registration form.
"""
import io
import os
import re
import sys

try:
    sys.stdout.reconfigure(encoding="utf-8", errors="replace")
except Exception:
    pass

BASE = os.path.dirname(os.path.abspath(__file__))

TARGETS = [
    "resources/views/student/registration/includes/student-common-script.blade.php",
    "resources/views/student/online-registration/includes/student-common-script.blade.php",
]

NEW_BLOCK = r"""    /* The Guardian buttons.

       Each one used to end with four lines like

           f.father_as_guardian.checked == false;

       which were wrong twice over: the radios are one group named guardian_is, so there is no
       field called father_as_guardian, and '==' compares rather than assigns. The lines never
       did anything, and reaching them threw a TypeError that stopped the function where it
       stood. A browser unchecks the rest of a radio group by itself, so they are gone.

       They also used to fill different sets of fields - Father and Mother set occupation and
       office, Self did not - so moving a student from Father to Self left the father's
       occupation sitting under the student's own name, and it saved that way. Every button now
       goes through setGuardian, which writes every guardian field on every press. That is what
       makes a stale value impossible, rather than merely fixed once. */

    function showGuardianDetail() {
        document.getElementById('guardian-detail').style.display = 'block';
        document.getElementById('link-guardian-detail').style.display = 'none';
        addRequiredFieldInGuardian();
    }

    /* A form field's value, or an empty string if this form does not carry that field.
       The registration form and the online registration form do not hold quite the same set,
       and one missing input used to take the whole button down with it. */
    function fieldValue(f, name) {
        return (f[name] && typeof f[name].value === 'string') ? f[name].value : '';
    }

    function setGuardian(f, values) {
        var fields = ['first_name', 'middle_name', 'last_name', 'eligibility', 'occupation',
            'office', 'office_number', 'residence_number', 'mobile_1', 'mobile_2', 'email',
            'address', 'relation'];

        for (var i = 0; i < fields.length; i++) {
            var box = f['guardian_' + fields[i]];
            if (!box || typeof box.value !== 'string') { continue; }
            box.value = values[fields[i]] || '';
        }
    }

    /*copy Father Detail on Guardian Detail*//*guardian_is*/
    function FatherAsGuardian(f) {
        showGuardianDetail();
        if (f.guardian_is.value != 'father_as_guardian') { return; }

        setGuardian(f, {
            first_name: fieldValue(f, 'father_first_name'),
            middle_name: fieldValue(f, 'father_middle_name'),
            last_name: fieldValue(f, 'father_last_name'),
            eligibility: fieldValue(f, 'father_eligibility'),
            occupation: fieldValue(f, 'father_occupation'),
            office: fieldValue(f, 'father_office'),
            office_number: fieldValue(f, 'father_office_number'),
            residence_number: fieldValue(f, 'father_residence_number'),
            mobile_1: fieldValue(f, 'father_mobile_1'),
            mobile_2: fieldValue(f, 'father_mobile_2'),
            email: fieldValue(f, 'father_email'),
            /* A parent has no address of their own on this form; the household address is the
               student's. Leaving it untouched meant a previous guardian's address stayed. */
            address: fieldValue(f, 'address'),
            relation: 'FATHER'
        });
    }

    /*copy Mother Detail on Guardian Detail*/
    function MotherAsGuardian(f) {
        showGuardianDetail();
        if (f.guardian_is.value != 'mother_as_guardian') { return; }

        setGuardian(f, {
            first_name: fieldValue(f, 'mother_first_name'),
            middle_name: fieldValue(f, 'mother_middle_name'),
            last_name: fieldValue(f, 'mother_last_name'),
            eligibility: fieldValue(f, 'mother_eligibility'),
            occupation: fieldValue(f, 'mother_occupation'),
            office: fieldValue(f, 'mother_office'),
            office_number: fieldValue(f, 'mother_office_number'),
            residence_number: fieldValue(f, 'mother_residence_number'),
            mobile_1: fieldValue(f, 'mother_mobile_1'),
            mobile_2: fieldValue(f, 'mother_mobile_2'),
            email: fieldValue(f, 'mother_email'),
            address: fieldValue(f, 'address'),
            relation: 'MOTHER'
        });
    }

    /*the student stands as their own guardian*/
    function SelfGuardian(f) {
        showGuardianDetail();
        if (f.guardian_is.value != 'self_guardian') { return; }

        /* Eligibility, occupation, office and office number are left blank on purpose: they
           belong to whoever the guardian was before, and a student is not a farmer because
           their father is. This is the case that was reported. */
        setGuardian(f, {
            first_name: fieldValue(f, 'first_name'),
            middle_name: fieldValue(f, 'middle_name'),
            last_name: fieldValue(f, 'last_name'),
            eligibility: '',
            occupation: '',
            office: '',
            office_number: '',
            residence_number: fieldValue(f, 'home_phone'),
            mobile_1: fieldValue(f, 'mobile_1'),
            mobile_2: fieldValue(f, 'mobile_2'),
            email: fieldValue(f, 'email'),
            address: fieldValue(f, 'address'),
            relation: 'SELF'
        });
    }

    /*Blank Guardian Detail to Enter New*/
    function OtherGuardian(f) {
        showGuardianDetail();
        if (f.guardian_is.value != 'other_guardian') { return; }

        /* Everything, including the address - which used to be left behind, so a brand new
           guardian arrived carrying the last one's address. */
        setGuardian(f, {});
    }

    /* Declared with no parameter while the markup calls linkGuardian(this.form), so every line
       below read an undefined f and the button threw before it copied anything. */
    function linkGuardian(f) {
        document.getElementById('guardian-detail').style.display = 'none';
        document.getElementById('link-guardian-detail').style.display = 'block';
        removeRequiredFieldInGuardian();

        if (!f) { return; }

        setGuardian(f, {
            first_name: fieldValue(f, 'father_first_name'),
            middle_name: fieldValue(f, 'father_middle_name'),
            last_name: fieldValue(f, 'father_last_name'),
            eligibility: fieldValue(f, 'father_eligibility'),
            occupation: fieldValue(f, 'father_occupation'),
            office: fieldValue(f, 'father_office'),
            office_number: fieldValue(f, 'father_office_number'),
            residence_number: fieldValue(f, 'father_residence_number'),
            mobile_1: fieldValue(f, 'father_mobile_1'),
            mobile_2: fieldValue(f, 'father_mobile_2'),
            email: fieldValue(f, 'father_email'),
            address: fieldValue(f, 'address'),
            relation: 'FATHER'
        });
    }
"""

START = re.compile(r"[ \t]*/\*copy Father Detail on Guardian Detail\*/")
END = re.compile(r"[ \t]*function addRequiredFieldInGuardian\(\)")

changed = 0
for rel in TARGETS:
    path = os.path.join(BASE, rel.replace("/", os.sep))
    if not os.path.exists(path):
        print("  missing: " + rel)
        continue

    raw = io.open(path, "rb").read()
    crlf = raw.count(b"\r\n")
    nl = raw.count(b"\n")
    text = raw.decode("utf-8")
    if crlf and crlf == nl:
        text = text.replace("\r\n", "\n")
        ending = "\r\n"
    elif crlf == 0:
        ending = "\n"
    else:
        print("  %s has mixed line endings - skipped so nothing is mangled" % rel)
        continue

    s = START.search(text)
    e = END.search(text, s.end() if s else 0)
    if not s or not e:
        print("  %s - could not find the guardian block, left alone" % rel)
        continue

    old = text[s.start():e.start()]

    """Only replace what we recognise. If this file has already been repaired, or was never
       broken, leave it exactly as it is rather than writing over something unexpected."""
    if "checked == false" not in old:
        print("  %s - already repaired or different, left alone" % rel)
        continue

    io.open(path, "wb").write(
        (text[:s.start()] + NEW_BLOCK + "\n" + text[e.start():]).replace("\n", ending).encode("utf-8")
        if ending == "\r\n" else
        (text[:s.start()] + NEW_BLOCK + "\n" + text[e.start():]).encode("utf-8"))

    print("  repaired %s (%d lines of the old block replaced)" % (rel, old.count("\n")))
    changed += 1

print("\n%d file(s) changed" % changed)
